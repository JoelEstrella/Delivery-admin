@if(session('success'))
    <div class="ui-alert ui-alert--success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="ui-alert ui-alert--error">
        {{ session('error') }}
    </div>
@endif
