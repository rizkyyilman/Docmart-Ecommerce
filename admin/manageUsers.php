<?php include 'includes/header.php'; ?>
<h2>Manage Users</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $connection->query("SELECT * FROM users");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>
                    <a href='deleteUser.php?id={$row['id']}' class='btn btn-sm btn-danger'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
