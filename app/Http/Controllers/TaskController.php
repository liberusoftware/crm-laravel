<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use App\Services\ReminderService;
use App\Services\TaskAssignmentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(
        protected ReminderService $reminderService,
        protected TaskAssignmentService $taskAssignmentService,
    ) {}

    public function index(): Factory|View
    {
        $tasks = Task::where('assigned_to', Auth::id())->orderBy('due_date')->get();

        return view('tasks.index', ['tasks' => $tasks]);
    }

    public function create(): Factory|View
    {
        $contacts = Contact::all();
        $leads = Lead::all();
        $users = User::all();

        return view('tasks.create', ['contacts' => $contacts, 'leads' => $leads, 'users' => $users]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'recurrence' => 'nullable|in:daily,weekly,monthly',
            'contact_id' => 'nullable|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'reminder_date' => 'nullable|date|before_or_equal:due_date',
        ]);

        $task = Task::create($validatedData);
        $this->taskAssignmentService->notify($task);

        if ($request->has('reminder_date')) {
            $this->reminderService->scheduleReminder($task, $request->reminder_date);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task): Factory|View
    {
        $contacts = Contact::all();
        $leads = Lead::all();
        $users = User::all();

        return view('tasks.edit', ['task' => $task, 'contacts' => $contacts, 'leads' => $leads, 'users' => $users]);
    }

    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'recurrence' => 'nullable|in:daily,weekly,monthly',
            'contact_id' => 'nullable|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'assigned_to' => 'required|exists:users,id',
            'reminder_date' => 'nullable|date|before_or_equal:due_date',
        ]);

        $previousAssigneeId = $task->assigned_to;
        $task->update($validatedData);
        $this->taskAssignmentService->notify($task, $previousAssigneeId);

        if ($request->has('reminder_date')) {
            $this->reminderService->scheduleReminder($task, $request->reminder_date);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function complete(Task $task)
    {
        $task->markAsComplete();

        return redirect()->back()->with('success', 'Task marked as complete.');
    }

    public function incomplete(Task $task)
    {
        $task->markAsIncomplete();

        return redirect()->back()->with('success', 'Task marked as incomplete.');
    }

    public function assign(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $previousAssigneeId = $task->assigned_to;
        $task->assign($user);
        $this->taskAssignmentService->notify($task, $previousAssigneeId);

        return redirect()->back()->with('success', 'Task assigned successfully.');
    }
}
