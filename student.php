<?php
require_once('_autoload.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <?php require_once('_head_asset.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .error-feedback { color: red; font-size: 0.9em; }
        .lbl-required:after { content: " *"; color: red; }
    </style>
</head>
<body class="bg-light">

<?php require_once('_navbar.php'); ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-10 offset-1">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Students</h3>
                </div>
                <div class="card-body">
                    <button class="btn btn-success mb-3" id="btnAddStudent">Add Student</button>
                    <table id="studentsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="studentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="studentId" name="id">
                    <div class="mb-3">
                        <label for="studentName" class="form-label lbl-required">Name</label>
                        <input type="text" class="form-control" id="studentName" name="name">
                        <div class="error-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="studentEmail" class="form-label lbl-required">Email</label>
                        <input type="email" class="form-control" id="studentEmail" name="email">
                        <div class="error-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="studentCourse" class="form-label lbl-required">Course</label>
                        <input type="text" class="form-control" id="studentCourse" name="course">
                        <div class="error-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStudent">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    let table = $('#studentsTable').DataTable({
        ajax: 'student_.php?action=read',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'course' },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-warning btnEdit" data-id="${data.id}">Edit</button>
                        <button class="btn btn-sm btn-danger btnDelete" data-id="${data.id}">Delete</button>
                    `;
                }
            }
        ]
    });

    function clearFormErrors() {
        $('#studentForm input').removeClass('is-invalid');
        $('#studentForm .error-feedback').text('');
    }

    $('#studentForm input').on('input', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.error-feedback').text('');
    });

    $('#btnAddStudent').click(function() {
        $('#studentForm')[0].reset();
        $('#studentId').val('');
        $('#studentModalLabel').text('Add Student');
        clearFormErrors();
        $('#studentModal').modal('show');
    });

    $('#saveStudent').click(function() {
        clearFormErrors();
        $.post('student_.php?action=save', $('#studentForm').serialize(), function(res) {
            if (res.status === 'success') {
                toastr.success(res.message);
                $('#studentModal').modal('hide');
                table.ajax.reload();
            } else {
                toastr.error(res.message);
                if (res.errors) {
                    for (const field in res.errors) {
                        const inputId = '#student' + field.charAt(0).toUpperCase() + field.slice(1);
                        $(inputId).addClass('is-invalid');
                        $(inputId).next('.error-feedback').text(res.errors[field]);
                    }
                }
            }
        }, 'json');
    });

    $('#studentsTable').on('click', '.btnEdit', function() {
        const id = $(this).data('id');
        $.get('student_.php?action=edit&id=' + id, function(data) {
            $('#studentId').val(data.id);
            $('#studentName').val(data.name);
            $('#studentEmail').val(data.email);
            $('#studentCourse').val(data.course);
            $('#studentModalLabel').text('Edit Student');
            clearFormErrors();
            $('#studentModal').modal('show');
        });
    });

    $('#studentsTable').on('click', '.btnDelete', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this student?')) {
            $.post('student_.php?action=delete', { id }, function(res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    table.ajax.reload();
                } else {
                    toastr.error(res.message);
                }
            }, 'json');
        }
    });
});
</script>

<?php require_once('_footer.php'); ?>
</body>
</html>
