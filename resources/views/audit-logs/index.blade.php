@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Audit Logs</h1>
    <p class="text-gray-600 mt-1">Track all system changes and activities</p>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="{{ route('audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="label">Action</label>
            <select name="action" class="input-field">
                <option value="">All Actions</option>
                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
        </div>
        <div>
            <label class="label">Table</label>
            <select name="table_name" class="input-field">
                <option value="">All Tables</option>
                <option value="contracts" {{ request('table_name') == 'contracts' ? 'selected' : '' }}>Contracts</option>
            </select>
        </div>
        <div>
            <label class="label">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field">
        </div>
        <div>
            <label class="label">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field">
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="btn-primary flex-1">Filter</button>
            <a href="{{ route('audit-logs.index') }}" class="btn-secondary">Clear</a>
        </div>
    </form>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    <td>{{ $log->user->username ?? 'System' }}</td>
                    <td>
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            {{ $log->action == 'created' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $log->action == 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $log->action == 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td>{{ $log->table_name }}</td>
                    <td>{{ $log->record_id }}</td>
                    <td>
                        <button onclick="toggleDetails({{ $log->id }})" class="text-blue-600 hover:underline text-sm">
                            View Details
                        </button>
                        <div id="details-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded text-xs">
                            @if($log->old_values)
                            <div class="mb-2">
                                <strong>Old Values:</strong>
                                <pre class="mt-1 overflow-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                            @if($log->new_values)
                            <div>
                                <strong>New Values:</strong>
                                <pre class="mt-1 overflow-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-8">No audit logs found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

<script>
function toggleDetails(id) {
    const element = document.getElementById('details-' + id);
    element.classList.toggle('hidden');
}
</script>
@endsection

