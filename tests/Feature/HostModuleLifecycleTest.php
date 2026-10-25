<?php

use App\Events\Module\ModuleDisabled;
use App\Events\Module\ModuleEnabled;
use App\Events\Module\ModuleInstalled;
use App\Events\Module\ModuleUninstalled;
use App\Models\Module;
use App\Modules\BaseModule;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Tests\Fixtures\HostModules\primary\Alpha\AlphaModule;
use Tests\Fixtures\HostModules\primary\Alpha\Providers\AlphaServiceProvider;

beforeEach(function () {
    $this->fixtureRoot = base_path('tests/Fixtures/HostModules/primary');
    foreach (['Alpha/AlphaModule.php', 'Beta/BetaModule.php', 'Invalid/InvalidModule.php', 'Alpha/Providers/AlphaServiceProvider.php'] as $file) {
        require_once $this->fixtureRoot.'/'.$file;
    }
    if (! class_exists('App\\Modules\\Alpha\\Providers\\AlphaServiceProvider', false)) {
        class_alias(AlphaServiceProvider::class, 'App\\Modules\\Alpha\\Providers\\AlphaServiceProvider');
    }
    config(['modules.path' => $this->fixtureRoot, 'modules.external_paths' => ['tests/Fixtures/HostModules/absent'], 'modules.load_composer' => false, 'modules.enabled' => ['Alpha'], 'modules.development' => false, 'modules.cache_key' => 'host.fixture.modules', 'modular.namespace' => 'Tests\\Fixtures\\HostModules']);
    Event::fake([ModuleEnabled::class, ModuleDisabled::class, ModuleInstalled::class, ModuleUninstalled::class]);
});

afterEach(function () {
    File::deleteDirectory(public_path('modules/Alpha'));
});

it('discovers valid modules and exposes their metadata, state and health', function () {
    $manager = new ModuleManager();
    expect($manager->all())->toHaveCount(2)
        ->and($manager->enabled())->toHaveCount(1)
        ->and($manager->disabled())->toHaveCount(1)
        ->and($manager->has('Alpha'))->toBeTrue()
        ->and($manager->has('absent'))->toBeFalse()
        ->and($manager->get('absent'))->toBeNull()
        ->and($manager->getModuleInfo('absent'))->toBe([])
        ->and($manager->getModuleInfo('Alpha'))->toMatchArray(['name' => 'Alpha', 'version' => '2.1.0', 'description' => 'Lifecycle integration fixture', 'dependencies' => [], 'enabled' => true, 'config' => ['feature' => true]])
        ->and($manager->getAllModulesInfo())->toHaveCount(2)
        ->and($manager->checkHealth()['Alpha']['status'])->toBe('healthy')
        ->and($manager->checkHealth()['Beta']['status'])->toBe('degraded');
    foreach (['enable', 'disable', 'install', 'uninstall'] as $action) {
        expect($manager->$action('absent'))->toBeFalse();
    }
});

it('enforces dependency order and persists lifecycle events', function () {
    $manager = new ModuleManager();
    expect($manager->enable('Beta'))->toBeTrue();
    foreach (['disable', 'uninstall'] as $action) {
        expect(fn () => $manager->$action('Alpha'))->toThrow(Exception::class, 'depend on it');
    }
    expect($manager->disable('Beta'))->toBeTrue()
        ->and($manager->uninstall('Beta'))->toBeTrue()
        ->and($manager->disable('Alpha'))->toBeTrue();
    foreach (['enable', 'install'] as $action) {
        expect(fn () => $manager->$action('Beta'))->toThrow(Exception::class, 'unmet dependencies');
    }
    Event::assertDispatched(ModuleEnabled::class);
    Event::assertDispatched(ModuleDisabled::class);
    Event::assertDispatched(ModuleUninstalled::class);
    $this->assertDatabaseHas('modules', ['name' => 'Alpha', 'is_enabled' => false]);
});

it('installs module migrations and published assets from the actual external path', function () {
    $provider = new ModuleServiceProvider($this->app);
    $provider->register();
    $provider->boot();
    $manager = new ModuleManager();
    expect($manager->install('Alpha'))->toBeTrue()
        ->and(Schema::hasTable('host_fixture_rows'))->toBeTrue()
        ->and(File::get(public_path('modules/Alpha/example.txt')))->toContain('Published fixture asset');
    Event::assertDispatched(ModuleInstalled::class);
    expect($manager->uninstall('Alpha'))->toBeTrue()
        ->and(File::exists(public_path('modules/Alpha')))->toBeFalse();
});

it('rebuilds modules after invalidating all enabled-state caches', function () {
    $manager = new ModuleManager();
    expect($manager->get('Alpha')->isEnabled())->toBeTrue();
    Module::query()->create(['name' => 'Alpha', 'is_enabled' => false]);
    Cache::put('host.fixture.modules', 'stale');
    $manager->rebuild();
    expect(Cache::get('host.fixture.modules'))->toBeNull()
        ->and($manager->get('Alpha')->isEnabled())->toBeFalse();
    $manager->clearCache();
    expect(Cache::get('module.Alpha.enabled'))->toBeNull();
});

it('loads provider configuration, routes, views, translations and publication mappings', function () {
    config(['alpha.options.override' => 'custom']);
    $provider = new ModuleServiceProvider($this->app);
    $provider->register();
    $provider->boot();
    expect($this->app['host.fixture.registered'])->toBeTrue()
        ->and(config('alpha.options'))->toBe(['label' => 'fixture', 'override' => 'custom'])
        ->and(trim(view('alpha::welcome', ['name' => 'Ada'])->render()))->toBe('Hello Ada')
        ->and(trans('alpha::messages.hello', [], 'en'))->toBe('Fixture translation')
        ->and($this->app['migrator']->paths())->toContain($this->fixtureRoot.'/Alpha/database/migrations');
    foreach (['web', 'api', 'admin'] as $kind) {
        $this->get('/host-fixture/'.$kind)->assertOk()->assertSeeText($kind.' fixture');
    }
    expect(ServiceProvider::pathsToPublish(null, 'alpha-config'))->toHaveKey($this->fixtureRoot.'/Alpha/config/options.php');
});

it('loads external and composer module directories and handles missing primary directories', function () {
    config(['modules.path' => $this->fixtureRoot.'/absent', 'modules.external_paths' => ['tests/Fixtures/HostModules/primary'], 'modules.load_composer' => true, 'modular.module_directory' => 'tests/Fixtures/HostModules/primary']);
    expect((new ModuleManager())->all())->toHaveCount(2);
    $provider = new ModuleServiceProvider($this->app);
    $provider->register();
    $provider->boot();
    config(['modular.module_directory' => 'tests/Fixtures/HostModules/missing']);
    expect((new ModuleManager())->all())->toHaveCount(2);
    (new ModuleServiceProvider($this->app))->register();
});

it('loads legacy primary module namespaces and ignores absent module classes', function () {
    if (! class_exists('App\\Modules\\Alpha\\AlphaModule', false)) {
        class_alias(AlphaModule::class, 'App\\Modules\\Alpha\\AlphaModule');
    }
    $manager = new class() extends ModuleManager
    {
        public function loadFixture(string $name): void
        {
            $this->loadModule($name, app_path('Modules/'.$name));
        }
    };
    $manager->loadFixture('Alpha');
    $manager->loadFixture('Missing');
    expect($manager->has('Alpha'))->toBeTrue()->and($manager->has('Missing'))->toBeFalse();
});

it('uses persisted state in development and falls back safely when the database is unavailable', function () {
    $module = new AlphaModule();
    config(['modules.development' => true]);
    $module->disable();
    Cache::put('module.Alpha.enabled', true);
    expect($module->isEnabled())->toBeFalse();
    $module->enable();
    expect($module->isEnabled())->toBeTrue();
    Schema::drop('modules');
    config(['modules.development' => false, 'modules.enabled' => ['Other']]);
    Cache::forget('module.Alpha.enabled');
    expect($module->isEnabled())->toBeFalse();
    $module->enable();
    expect(Cache::get('module.Alpha.enabled'))->toBeTrue();
    $module->disable();
    expect(Cache::get('module.Alpha.enabled'))->toBeFalse();
});

it('uses safe metadata defaults for a module without a manifest', function () {
    $module = new class() extends BaseModule {};
    config(['modules.enabled' => [], 'modules.development' => true]);
    expect($module->getName())->toBe(class_basename($module))
        ->and($module->getVersion())->toBe('1.0.0')
        ->and($module->getDescription())->toBe('')
        ->and($module->getDependencies())->toBe([])
        ->and($module->getConfig())->toBe([])
        ->and($module->checkHealth()['status'])->toBe('degraded');
});
