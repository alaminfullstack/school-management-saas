<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolRoutine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolRoutineController extends Controller
{
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        $query = SchoolRoutine::with(['school_class', 'school_group', 'school_section', 'school_subject', 'teacher'])
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

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('day_name', 'like', "%$s%")
                    ->orWhereHas('school_class', function ($c) use ($s) {
                        $c->where('class_name', 'like', "%$s%");
                    })
                    ->orWhereHas('teacher', function ($t) use ($s) {
                        $t->where('name', 'like', "%$s%");
                    });
            });
        }

        return response()->json($query->orderBy('day_name')->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());
        $data = $request->validate([
            'class_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'day_name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'group_id' => 'nullable',
            'section_id' => 'nullable',
        ]);

        $data['school_id'] = $school->id;
        $routine = SchoolRoutine::create($data);
        return response()->json($routine);
    }

    public function show($id)
    {
        return SchoolRoutine::with(['school_class', 'school_group', 'school_section', 'school_subject', 'teacher'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $routine = SchoolRoutine::findOrFail($id);
        $routine->update($request->all());
        return response()->json($routine);
    }

    public function destroy($id)
    {
        SchoolRoutine::destroy($id);
        return response()->json(['message' => 'Deleted Successfully']);
    }
    
    //Routine filter
    public function routineFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        $query = SchoolRoutine::with([
            'school_class',
            'school_group',
            'school_section',
            'school_subject',
            'teacher'
        ])->where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $data = $query->orderBy('day_name')->latest()->paginate(15);

        if ($data->total() == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No routine found for selected filter.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Routine Filter List',
            'data' => $data
        ]);
    }
    
}