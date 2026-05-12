<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolSessionController extends Controller
{
    /**
     * Helper to get the school associated with the authenticated user.
     */
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['data' => [], 'total' => 0]);

        $query = SchoolSession::with(['schoolClass', 'schoolGroup', 'schoolSection'])
            ->where('school_id', $school->id);

        // Filter by class_id
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by group_id
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section_id
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by session_year
        if ($request->filled('session_year')) {
            $query->where('session_year', $request->session_year);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('session_year', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

        $request->validate([
            'class_id'     => 'required|exists:school_classes,id',
            'group_id'     => 'nullable|exists:school_groups,id',
            'section_id'   => 'nullable|exists:school_sections,id',
            'session_year' => 'required|string',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'total_days'   => 'required|integer',
        ]);

        /**
         * Duplicate Check:
         * Prevents creating a duplicate session for the same
         * Class + Group + Section + Session Year combination.
         */
        $exists = SchoolSession::where([
            'school_id'    => $school->id,
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_id'   => $request->section_id,
            'session_year' => $request->session_year,
        ])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Session already exists.'
            ], 422);
        }

        $session = SchoolSession::create([
            'school_id'    => $school->id,
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_id'   => $request->section_id,
            'session_year' => $request->session_year,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'total_days'   => $request->total_days,
        ]);

        return response()->json(['message' => 'Session created successfully', 'data' => $session]);
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);
        return response()->json($session);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);

        $request->validate([
            'class_id'     => 'required|exists:school_classes,id',
            'group_id'     => 'nullable|exists:school_groups,id',
            'section_id'   => 'nullable|exists:school_sections,id',
            'session_year' => 'required|string',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'total_days'   => 'required|integer',
        ]);

        /**
         * Update Duplicate Check:
         * Ensures the updated Class + Group + Section + Session Year
         * does not conflict with another existing record.
         */
        $exists = SchoolSession::where([
            'school_id'    => $school->id,
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_id'   => $request->section_id,
            'session_year' => $request->session_year,
        ])->where('id', '!=', $id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Session already exists.'
            ], 422);
        }

        $session->update([
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_id'   => $request->section_id,
            'session_year' => $request->session_year,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'total_days'   => $request->total_days,
        ]);

        return response()->json(['message' => 'Session updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);

        $session->delete();
        return response()->json(['message' => 'Session deleted successfully']);
    }
    
        //School Session Filter
    public function sessionFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSession::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection'
            ])
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('session_year', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolGroup', function ($sg) use ($search) {
                    $sg->where('group_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolSection', function ($ss) use ($search) {
                    $ss->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $sessions = $query->latest()->paginate(10);

        if ($sessions->total() == 0) {
            return response()->json([
                'message' => 'No session found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($sessions);
    }
    
    
    
    
}