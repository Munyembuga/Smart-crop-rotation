@extends('layouts.admin')

@section('title', 'Farmer Management - Smart Crop Rotation')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    /* Enhanced table styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    .table thead th {
        background-color: #f8f9fa;
        padding: 15px;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        border-radius: 6px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced modals */
    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 25px;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 20px 25px;
    }

    /* Form controls */
    .form-control {
        border-radius: 6px;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        min-height: 45px;
    }

    .form-control:focus {
        border-color: #0ac15e;
        box-shadow: 0 0 0 0.2rem rgba(10, 193, 94, 0.25);
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
    }

    /* Pagination */
    .pagination {
        margin: 20px 0 0;
    }

    .page-item.active .page-link {
        background-color: #0ac15e;
        border-color: #0ac15e;
    }

    .page-link {
        color: #0ac15e;
        border-radius: 4px;
        margin: 0 3px;
    }

    /* Card header */
    .card-header {
        padding: 20px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    /* Enhanced notifications */
    .toast-success {
        background-color: #0ac15e !important;
    }

    .toast-error {
        background-color: #dc3545 !important;
    }

    /* Modal details styling */
    .farmer-detail {
        padding: 12px 18px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin-bottom: 12px;
    }

    .farmer-detail strong {
        display: block;
        color: #6c757d;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .farmer-detail span {
        font-size: 1.1rem;
        color: #212529;
    }

    /* Badge styles for status */
    .badge {
        padding: 8px 12px;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .badge-success {
        background-color: #e3fcef;
        color: #0ac15e;
    }
</style>
@endsection

@section('content')
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Farmer Management</h2>
        <button class="btn" id="openAddFarmerModal" style="display: flex; align-items: center; background-color: #0ac15e; color: white; padding: 10px 20px; font-weight: 600;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px;">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Farmer
        </button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Location</th>
                <th style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Fetch all users with farmer role (role_id = 1)
                $farmers = \App\Models\User::where('role_id', 1)->paginate(5);
            @endphp

            @forelse($farmers as $farmer)
                <tr>
                    <td>{{ $farmer->name }}</td>
                    <td>{{ $farmer->phone ?? 'N/A' }}</td>
                    <td>{{ $farmer->email }}</td>
                    <td>{{ $farmer->location ?? 'N/A' }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn view-farmer" data-id="{{ $farmer->id }}" style="padding: 8px 16px; font-size: 0.875rem; background-color: #0ac15e; color: white;">
                                <i class="fas fa-eye" style="margin-right: 5px;"></i> View
                            </button>
                            <button class="btn edit-farmer" data-id="{{ $farmer->id }}" style="padding: 8px 16px; font-size: 0.875rem; background-color: #007bff; color: white;">
                                <i class="fas fa-edit" style="margin-right: 5px;"></i> Edit
                            </button>
                            <button class="btn delete-farmer" data-id="{{ $farmer->id }}" style="padding: 8px 16px; font-size: 0.875rem; background-color: #dc3545; color: white;">
                                <i class="fas fa-trash" style="margin-right: 5px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 30px;">
                        <div style="color: #6c757d;">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 15px; color: #e9ecef;"></i>
                            <p style="font-size: 1.1rem;">No farmers found</p>
                            <p style="font-size: 0.9rem;">Click "Add Farmer" to add your first farmer</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
        {{ $farmers->links() }}
    </div>

    <!-- Add Farmer Modal -->
    <div class="modal fade" id="addFarmerModal" tabindex="-1" role="dialog" aria-labelledby="addFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0ac15e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <h5 class="modal-title" id="addFarmerModalLabel" style="font-weight: 700; margin: 0;">Add New Farmer</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addFarmerForm" action="{{ route('admin.farmers.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="+250 7XX XXX XXX">
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" placeholder="Enter location">
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                        </div>

                        <div class="modal-footer justify-content-between" style="padding-left: 0; padding-right: 0;">
                            <button type="button" class="btn" style="background-color: #f8f9fa; color: #6c757d;" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" style="background-color: #0ac15e; color: white; padding-left: 30px; padding-right: 30px;">
                                <i class="fas fa-save mr-1"></i> Save Farmer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Farmer Modal -->
    <div class="modal fade" id="viewFarmerModal" tabindex="-1" role="dialog" aria-labelledby="viewFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0ac15e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                        </svg>
                        <h5 class="modal-title" id="viewFarmerModalLabel" style="font-weight: 700; margin: 0;">Farmer Details</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="farmer-detail">
                        <strong>Full Name</strong>
                        <span id="view-name"></span>
                    </div>
                    <div class="farmer-detail">
                        <strong>Email Address</strong>
                        <span id="view-email"></span>
                    </div>
                    <div class="farmer-detail">
                        <strong>Phone Number</strong>
                        <span id="view-phone"></span>
                    </div>
                    <div class="farmer-detail">
                        <strong>Location</strong>
                        <span id="view-location"></span>
                    </div>
                    <div class="farmer-detail">
                        <strong>Registered On</strong>
                        <span id="view-created"></span>
                    </div>
                    <div class="farmer-detail">
                        <strong>Account Status</strong>
                        <span><span class="badge badge-success" id="view-status"></span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" style="background-color: #f8f9fa; color: #6c757d;" data-dismiss="modal">Close</button>
                    <button type="button" class="btn edit-from-view" style="background-color: #007bff; color: white;">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Farmer Modal -->
    <div class="modal fade" id="editFarmerModal" tabindex="-1" role="dialog" aria-labelledby="editFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div style="display: flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 10px;">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <h5 class="modal-title" id="editFarmerModalLabel">Edit Farmer</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editFarmerForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group mb-3">
                            <label for="edit-name">Full Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-phone">Phone Number</label>
                            <input type="tel" class="form-control" id="edit-phone" name="phone">
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-location">Location</label>
                            <input type="text" class="form-control" id="edit-location" name="location">
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-password">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="edit-password" name="password" placeholder="Enter new password">
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit-password-confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="edit-password-confirmation" name="password_confirmation" placeholder="Confirm new password">
                        </div>

                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn" style="background-color: #007bff; color: white;">Update Farmer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteFarmerModal" tabindex="-1" role="dialog" aria-labelledby="deleteFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dc3545; margin-bottom: 20px;"></i>
                    <h5 style="font-weight: 700; margin-bottom: 15px;">Delete Farmer</h5>
                    <p>Are you sure you want to delete this farmer? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-top-0 justify-content-center">
                    <button type="button" class="btn" style="background-color: #f8f9fa; color: #6c757d;" data-dismiss="modal">Cancel</button>
                    <form id="deleteFarmerForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toast notification configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000"
            };

            // Show success message from session
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif

            // Show error message from session
            @if(session('error'))
                toastr.error('{{ session('error') }}');
            @endif

            // Open the add modal with animation
            $('#openAddFarmerModal').on('click', function() {
                $('#addFarmerModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#addFarmerModal').on('shown.bs.modal', function() {
                    $('#name').focus();
                });
            });

            // View farmer details with improved data display
            $('.view-farmer').on('click', function() {
                let farmerId = $(this).data('id');
                let viewModal = $('#viewFarmerModal');

                // Store the ID for the edit button
                viewModal.data('farmer-id', farmerId);

                // Add loading state
                $('#view-name, #view-email, #view-phone, #view-location, #view-created, #view-status').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );

                // Show the modal immediately with loading indicators
                viewModal.modal('show');

                // Fetch farmer details via AJAX
                $.ajax({
                    url: `/admin/farmers/${farmerId}`,
                    method: 'GET',
                    success: function(response) {
                        // Populate the modal with farmer details
                        $('#view-name').text(response.name);
                        $('#view-email').text(response.email);
                        $('#view-phone').text(response.phone || 'N/A');
                        $('#view-location').text(response.location || 'N/A');
                        $('#view-created').text(new Date(response.created_at).toLocaleDateString('en-US', {
                            year: 'numeric', month: 'long', day: 'numeric'
                        }));
                        $('#view-status').text(response.status ? response.status.toUpperCase() : 'ACTIVE');
                    },
                    error: function(error) {
                        console.error('Error fetching farmer details', error);
                        toastr.error('Could not fetch farmer details');
                        viewModal.modal('hide');
                    }
                });
            });

            // Handle edit button in view modal
            $('.edit-from-view').on('click', function() {
                let farmerId = $('#viewFarmerModal').data('farmer-id');
                $('#viewFarmerModal').modal('hide');

                // Trigger click on the edit button with the same ID
                $(`.edit-farmer[data-id="${farmerId}"]`).trigger('click');
            });

            // Edit farmer with improved UI feedback
            $('.edit-farmer').on('click', function() {
                let farmerId = $(this).data('id');

                // Show loading spinner on button
                let $btn = $(this);
                let originalHtml = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
                $btn.prop('disabled', true);

                // Fetch farmer details for editing
                $.ajax({
                    url: `/admin/farmers/${farmerId}`,
                    method: 'GET',
                    success: function(response) {
                        // Set form action
                        $('#editFarmerForm').attr('action', `/admin/farmers/${farmerId}`);

                        // Populate the form
                        $('#edit-id').val(response.id);
                        $('#edit-name').val(response.name);
                        $('#edit-email').val(response.email);
                        $('#edit-phone').val(response.phone);
                        $('#edit-location').val(response.location);

                        // Show the modal
                        $('#editFarmerModal').modal('show');

                        // Reset button state
                        $btn.html(originalHtml);
                        $btn.prop('disabled', false);
                    },
                    error: function(error) {
                        console.error('Error fetching farmer details', error);
                        toastr.error('Could not fetch farmer details for editing');

                        // Reset button state
                        $btn.html(originalHtml);
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Delete farmer with confirmation
            $('.delete-farmer').on('click', function() {
                let farmerId = $(this).data('id');

                // Set form action for delete
                $('#deleteFarmerForm').attr('action', `/admin/farmers/${farmerId}`);

                // Show delete confirmation modal
                $('#deleteFarmerModal').modal('show');
            });

            // Form submission with AJAX
            $('#addFarmerForm').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);
                let $submitBtn = $form.find('button[type="submit"]');
                let originalBtnText = $submitBtn.html();

                // Disable button and show loading state
                $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        // Hide modal
                        $('#addFarmerModal').modal('hide');

                        // Show success message
                        toastr.success('Farmer added successfully!');

                        // Reload page after short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        // Reset button
                        $submitBtn.html(originalBtnText);
                        $submitBtn.prop('disabled', false);

                        // Display validation errors
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });

            // Similar AJAX handling for edit form
            $('#editFarmerForm').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);
                let $submitBtn = $form.find('button[type="submit"]');
                let originalBtnText = $submitBtn.html();

                // Disable button and show loading state
                $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        // Hide modal
                        $('#editFarmerModal').modal('hide');

                        // Show success message
                        toastr.success('Farmer updated successfully!');

                        // Reload page after short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        // Reset button
                        $submitBtn.html(originalBtnText);
                        $submitBtn.prop('disabled', false);

                        // Display validation errors
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });
        });
    </script>
    @endpush
@endsection
