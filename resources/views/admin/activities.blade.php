@extends('layouts.admin')

@section('content')
    <div class="dashboard-header">
        <h1>🕒 Activity Log</h1>
    </div>
    
    <div class="card">
        <div class="activity-list">
            @if(isset($activities) && count($activities) > 0)
                @foreach($activities as $activity)
                <div class="activity-item">
                    <div class="activity-icon">{{ $activity->icon ?? '📋' }}</div>
                    <div class="activity-content">
                        <div class="activity-text">{{ $activity->description }}</div>
                        <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="activity-item">
                    <div class="activity-icon">ℹ️</div>
                    <div class="activity-content">
                        <div class="activity-text">No activities recorded yet</div>
                        <div class="activity-time">-</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
