<?php
require_once('_autoload.php');

// Access control: only Admins
if ($_SESSION['urole'] !== 'Admin') {
	add_msg('error', 'Only administrators are allowed to manage users');
	redirect(PAGE_HOME);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User</title>
    <?php require_once('_head_asset.php'); ?>
</head>
<body class="bg-light">

<?php require_once('_navbar.php'); ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h3>Users Management</h3>
        </div>
        <div class="card-body">
            <button class="btn btn-success mb-3" id="btnAddUser">Add User</button>
            <table id="userTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title">User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3" id="passwordField">
                        <label id="passwordLabel">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function() {
    const table = $('#userTable').DataTable({
        ajax: 'user_.php?action=read',
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'username' },
            { data: 'role' },
            { data: 'created_at' },
            {
                data: null,
                render: data => `
                    <button class="btn btn-sm btn-warning btnEdit" data-id="${data.id}">Edit</button>
                    <button class="btn btn-sm btn-danger btnDelete" data-id="${data.id}">Delete</button>
                `
            }
        ]
    });

    $('#btnAddUser').click(() => {
		$('.modal-title').html('Add User');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#passwordLabel').text('Password');
        $('#passwordField').show();
        $('#userModal').modal('show');
    });

    $('#userTable').on('click', '.btnEdit', function() {
		$('.modal-title').html('Edit User');
        const id = $(this).data('id');
        $.getJSON('user_.php?action=edit&id=' + id, function(data) {
            $('#userId').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#username').val(data.username);
            $('#role').val(data.role);
            $('#password').val('');
            $('#passwordLabel').text('Password (leave blank to keep current)');
            $('#passwordField').show();
            $('#userModal').modal('show');
        });
    });

    $('#userTable').on('click', '.btnDelete', function() {
        if (confirm('Are you sure you want to delete this user?')) {
            $.post('user_.php', { action: 'delete', id: $(this).data('id') }, function(resp) {
                toastr[resp.status](resp.message);
                if (resp.status === 'success') table.ajax.reload();
            }, 'json');
        }
    });

    $('#userForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';
        $.post('user_.php', formData, function(resp) {
            toastr[resp.status](resp.message);
            if (resp.status === 'success') {
                $('#userModal').modal('hide');
                table.ajax.reload();
            }
        }, 'json');
    });
});
</script>

</body>
</html>
