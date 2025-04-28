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





<!-- Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentModalLabel">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="studentForm">
                    <input type="hidden" id="studentId" name="id">
                    <div class="mb-3">
                        <label for="studentName" class="form-label lbl-required">Name</label>
                        <input type="text" class="form-control" id="studentName" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="studentEmail" class="form-label lbl-required">Email</label>
                        <input type="email" class="form-control" id="studentEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="studentCourse" class="form-label lbl-required">Course</label>
                        <input type="text" class="form-control" id="studentCourse" name="course">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveStudent">Save</button>
            </div>
        </div>
    </div>
</div>

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

    $('#btnAddStudent').click(function() {
        $('#studentForm')[0].reset();
        $('#studentId').val('');
        $('#studentModalLabel').text('Add Student');
        $('#studentModal').modal('show');
    });

    $('#saveStudent').click(function() {
        $.post('student_.php?action=save', $('#studentForm').serialize(), function(res) {
            //const data = JSON.parse(res);
			const data = res;
            if (data.status === 'success') {
                toastr.success(data.message);
                $('#studentModal').modal('hide');
                table.ajax.reload();
            } else {
                toastr.error(data.message);
            }
        });
    });

    $('#studentsTable').on('click', '.btnEdit', function() {
        const id = $(this).data('id');
        $.get('student_.php?action=edit&id=' + id, function(res) {
            //const data = JSON.parse(res);
			const data = res;
            $('#studentId').val(data.id);
            $('#studentName').val(data.name);
            $('#studentEmail').val(data.email);
            $('#studentCourse').val(data.course);
            $('#studentModalLabel').text('Edit Student');
            $('#studentModal').modal('show');
        });
    });

    $('#studentsTable').on('click', '.btnDelete', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this student?')) {
            $.post('student_.php?action=delete', { id }, function(res) {
                //const data = JSON.parse(res);
				const data = res;
                if (data.status === 'success') {
                    toastr.success(data.message);
                    table.ajax.reload();
                } else {
                    toastr.error(data.message);
                }
            });
        }
    });
});
</script>


<?php require_once('_footer.php'); ?>


</body>
</html>
