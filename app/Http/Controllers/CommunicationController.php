<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Models\Classroom;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunicationController extends Controller
{
    /**
     * Display all communications with organization-wide visibility for Superadmin.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Superadmin has high-level organization-wide monitoring access
        if ($user->isSuperadmin() || $user->isPrincipal()) {
            $query = Communication::with(['sender', 'targetClassroom', 'recipients.recipient']);
        } else {
            // Standard user / Teacher / Parent / Student access to their own or targeted messages
            $query = Communication::with(['sender', 'targetClassroom', 'recipients.recipient'])
                ->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhereHas('recipients', function($rq) use ($user) {
                          $rq->where('recipient_id', $user->id);
                      });
                });
        }

        if ($request->filled('audience_type')) {
            $query->where('audience_type', $request->audience_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $communications = $query->latest()->paginate(15);
        $classrooms = Classroom::all();
        $totalMessages = Communication::count();

        return view('modules.communication', compact('communications', 'classrooms', 'totalMessages'));
    }

    /**
     * Create new Communication / Announcement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'audience_type' => ['required', 'string', 'in:all_parents,all_students,specific_classroom,direct_user'],
            'target_classroom_id' => ['nullable', 'exists:classrooms,id'],
            'recipient_ids' => ['nullable', 'array'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $sender = auth()->user();

            // 1. Create Communication Record
            $communication = Communication::create([
                'sender_id' => $sender->id,
                'campus_id' => $sender->campus_id ?? null,
                'audience_type' => $validated['audience_type'],
                'target_classroom_id' => $validated['target_classroom_id'] ?? null,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'status' => 'sent',
            ]);

            // 2. Resolve Target Recipients based on Audience Type
            $recipientUserIds = collect();

            if ($validated['audience_type'] === 'all_parents') {
                $recipientUserIds = User::whereHas('role', fn($q) => $q->where('name', 'parent'))->pluck('id');
            } elseif ($validated['audience_type'] === 'all_students') {
                $recipientUserIds = User::whereHas('role', fn($q) => $q->where('name', 'student'))->pluck('id');
            } elseif ($validated['audience_type'] === 'specific_classroom' && !empty($validated['target_classroom_id'])) {
                $classroom = Classroom::find($validated['target_classroom_id']);
                if ($classroom) {
                    $recipientUserIds = $classroom->students()->pluck('user_id');
                }
            } elseif (!empty($validated['recipient_ids'])) {
                $recipientUserIds = collect($validated['recipient_ids']);
            }

            // Insert recipient log records
            foreach ($recipientUserIds as $uId) {
                CommunicationRecipient::create([
                    'communication_id' => $communication->id,
                    'recipient_id' => $uId,
                    'is_read' => false,
                ]);
            }

            // Audit Log
            AuditLog::record('create_communication', 'communication', 'Communication', $communication->id, [
                'subject' => $communication->subject,
                'audience_type' => $communication->audience_type,
                'recipient_count' => $recipientUserIds->count(),
            ]);

            return back()->with('success', "Communication '{$communication->subject}' dispatched to {$recipientUserIds->count()} recipients.");
        });
    }

    /**
     * Show single Communication details and recipients.
     */
    public function show(Communication $communication)
    {
        $user = auth()->user();

        // Security check
        if (!$user->isSuperadmin() && !$user->isPrincipal() && $communication->sender_id !== $user->id) {
            $isRecipient = $communication->recipients()->where('recipient_id', $user->id)->exists();
            if (!$isRecipient) {
                abort(403, 'Unauthorized access to communication thread.');
            }
        }

        $communication->load(['sender', 'targetClassroom', 'recipients.recipient']);
        return view('modules.communication-detail', compact('communication'));
    }
}
