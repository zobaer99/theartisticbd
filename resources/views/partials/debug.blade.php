@if(config('app.debug') && isset($debug_info))
<div style="position: fixed; bottom: 10px; right: 10px; background: #333; color: white; padding: 10px; border-radius: 5px; font-size: 12px; z-index: 9999;">
    <strong>Debug Info:</strong><br>
    Queries: {{ $debug_info['query_count'] ?? 0 }}<br>
    Time: {{ $debug_info['execution_time'] ?? 0 }}ms<br>
    Memory: {{ $debug_info['memory_usage'] ?? 0 }}MB
</div>
@endif
