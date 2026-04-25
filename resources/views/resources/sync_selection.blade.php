@extends('admin.adminhome')
@section('content')
<div class="container-fluid">
    <div class="p-4 bg-white shadow-sm rounded-lg border-left border-danger mb-4">
        <h4 class="font-weight-bold"><i class="fab fa-youtube text-danger mr-2"></i> Select Videos with #MRSM</h4>
        {{-- Update the strong tag on line 6 to this: --}}
<strong>{{ \App\Models\Group::find($group_id)?->group_name ?? 'General Resource' }}</strong>
    </div>

    <form action="{{ route('resources.sync.store_selected') }}" method="POST">
        @csrf
        <input type="hidden" name="group_id" value="{{ $group_id }}">
        <input type="hidden" name="subject_id" value="{{ $subject_id }}">

        <div class="row">
            @forelse($youtubeVideos as $video)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                    <img src="{{ $video['thumbnail'] }}" class="card-img-top">
                    <div class="card-body">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="video_ids[{{ $video['id'] }}]" value="{{ $video['title'] }}" class="custom-control-input" id="vid-{{ $video['id'] }}">
                            <label class="custom-control-label font-weight-bold small" for="vid-{{ $video['id'] }}">{{ $video['title'] }}</label>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">No videos found on your channel with #MRSM.</div>
            @endforelse
        </div>

        @if(count($youtubeVideos) > 0)
        <div class="text-right mt-4 p-3 bg-white sticky-bottom shadow-lg" style="border-radius: 12px 12px 0 0;">
            <a href="{{ route('resources.index') }}" class="btn btn-light mr-2 font-weight-bold">Cancel</a>
            <button type="submit" class="btn btn-danger px-5 font-weight-bold shadow">Import Selected Videos</button>
        </div>
        @endif
    </form>
</div>
@endsection