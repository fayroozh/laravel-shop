
@extends('layouts.admin')
@section('content')
    <div class="dashboard-header">
        <h1>📝 Feedback Management</h1>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <table class="styled-table">
            <tr>
                <th>ID</th><th>Name</th><th>Feedback</th><th>Date</th><th>Actions</th>
            </tr>
            @foreach($feedback as $f)
                <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->user->name ?? $f->name }}</td>
                    <td>{{ $f->feedback }}</td>
                    <td>{{ $f->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="#" class="btn-view" onclick="openModal('viewFeedbackModal{{ $f->id }}')" title="View">👁️</a>
                        <form method="POST" action="{{ route('admin.feedback.destroy', $f->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button class="btn-delete" onclick="return confirm('Are you sure?')" title="Delete">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    
    <!-- View Feedback Modals -->
    @foreach($feedback as $f)
    <div id="viewFeedbackModal{{ $f->id }}" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewFeedbackModal{{ $f->id }}')">&times;</span>
            <h2>Feedback Details</h2>
            <div class="form-group">
                <label>From:</label>
                <p>{{ $f->user->name ?? $f->name }}</p>
            </div>
            <div class="form-group">
                <label>Date:</label>
                <p>{{ $f->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div class="form-group">
                <label>Feedback:</label>
                <p>{{ $f->feedback }}</p>
            </div>
            <form method="POST" action="{{ route('admin.feedback.destroy', $f->id) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this feedback?')">Delete Feedback</button>
            </form>
        </div>
    </div>
    @endforeach
@endsection