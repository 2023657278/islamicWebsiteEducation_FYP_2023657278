<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $subjects = Subject::all();
        //dd ($subjects);
        return view("subjects.index", compact("subjects"));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("subjects.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',
        ]);

        DB::table('subjects')->insert([
            'subject_code' => $request->subject_code,
            'subject_name'=> $request->subject_name,

        ]);

        return redirect()->route('subjects.index')
                        ->with('success','Subject created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
{
    // Fetch unique teaching assignments for this subject from the timetable
    $assignments = \App\Models\Timetable::where('subject_id', $subject->id)
        ->with(['teacher', 'group'])
        ->get()
        ->unique(function ($item) {
            // This ensures we only show "Teacher A teaching Group B" once
            return $item->teacher_id . $item->group_id;
        });

    return view('subjects.show', compact('subject', 'assignments'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
        return view('subjects.edit',compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        //
        $request->validate([
            'subject_code' => 'required',
            'subject_name' => 'required',
        ]);

        DB::table('subjects')->where('id',$request->id)->update([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
        ]);
  
        // $student->update($request->all());
  
        return redirect()->route('subjects.index')
                        ->with('success','Subject updated successfully');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
        $subject->delete();
  
        return redirect()->route('subjects.index')
                        ->with('success','Subject deleted successfully');
    }
}
