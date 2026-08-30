<div class="space-y-4" wire:loading.class="opacity-50">
    <div><label for="white-label-brand">Brand name</label><input id="white-label-brand" type="text" wire:model="brandName"></div>
    <div><label for="white-label-domain">Custom domain</label><input id="white-label-domain" type="text" wire:model="customDomain" placeholder="crm.example.com"></div>
    <div><label for="white-label-theme">Theme</label><input id="white-label-theme" type="text" wire:model="theme"></div>
    <div><label for="white-label-provider">Provider</label><input id="white-label-provider" type="text" wire:model="provider"></div>
    <label><input type="checkbox" wire:model="showPlatformAttribution"> Show platform attribution</label>
    <button type="button" wire:click="save">Save white-label settings</button>
    @error('authorization')<p role="alert">{{ $message }}</p>@enderror
    @error('version')<p role="alert">{{ $message }}</p>@enderror
</div>
