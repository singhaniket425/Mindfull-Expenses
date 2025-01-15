<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('header.php');
checkUser();
userArea();

// Handle Deletion
if (isset($_GET['type']) && $_GET['type'] === 'delete' && isset($_GET['id']) && $_GET['id'] > 0) {
    $id = get_safe_value($_GET['id']);

    $stmt = $con->prepare("DELETE FROM expense WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Data deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error deleting data.</div>";
    }
}

// Fetch Expenses
$query = "SELECT expense.*, category.name 
          FROM expense 
          INNER JOIN category ON expense.category_id = category.id 
          WHERE expense.added_by = '".$_SESSION['UID']."' 
          ORDER BY expense.expense_date ASC";
$res = mysqli_query($con, $query);
if (!$res) {
    die("Query failed: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="custom.css">
    <!-- Optional: Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTTXJWUdDk1UqylXYP2Uy2IuqbeF7oR5W0PVjG0rZL9P1KkC4jNuU0KCkXZGd4hVZQOYv9q5Mw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Additional Styles Specific to this Page (if any) */
    </style>
    <script>
        // Confirmation Dialog for Deletion
        function delete_confirm(id) {
            if (confirm("Are you sure you want to delete this expense?")) {
                window.location.href = "expense.php?type=delete&id=" + id;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Expense Report</h2>
        <a href="manage_expense.php" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Add Expense</a>
        
        <?php if (mysqli_num_rows($res) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Item</th>
                            <th>Price ($)</th>
                            <th>Details</th>
                            <th>Expense Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $final_price = 0;
                        while ($row = mysqli_fetch_assoc($res)): 
                            $final_price += $row['price'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['item']); ?></td>
                                <td><?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['details']); ?></td>
                                <td><?php echo htmlspecialchars($row['expense_date']); ?></td>
                                <td>
                                    <a href="manage_expense.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i> Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="delete_confirm(<?php echo htmlspecialchars($row['id']); ?>)"><i class="fas fa-trash-alt"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total:</th>
                            <th><?php echo number_format($final_price, 2); ?></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No data found.</div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXDv6tAD/L0hQKfVjVmJROfvhq0pjRwE2HcTJXf7h9q0WHH6BzQ5gkbvPj3GlDW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-p5fkgj2ZpG4N1nIYZ+Yy5o75Pn6jfzq5m06gOmn6QlF/nhdjVbVVVDt/Ddk14enT" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
