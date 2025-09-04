@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Lees</h2>
                <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                    <i class="fas fa-plus"></i> Upload Video
                </button>
            </div>
            
            <div id="lees-feed">
                <lees-feed></lees-feed>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Lees Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <lees-upload-form></lees-upload-form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.App = window.App || {};
window.App.config = {
    apiUrl: '{{ config("app.url") }}/api/v1.1',
    csrfToken: '{{ csrf_token() }}'
};
</script>
@endpush