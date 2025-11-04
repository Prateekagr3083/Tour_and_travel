<!-- Add Tour Form (Initially Hidden) -->
<div id="add-tour-form" class="add-tour-form" style="display: none;">
    <h3>Add New Tour</h3>
    <form id="tour-form" action="process_add_tour.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tour-name">Tour Name:</label>
            <input type="text" id="tour-name" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="destination">Destination:</label>
            <select id="destination" name="destination_id" required>
                <option value="">Select Destination</option>
                <?php
                include '../Database/db_connect.php';
                $dest_sql = "SELECT id, name FROM destinations ORDER BY name";
                $dest_result = $conn->query($dest_sql);
                while ($dest = $dest_result->fetch_assoc()) {
                    echo '<option value="' . $dest['id'] . '">' . htmlspecialchars($dest['name']) . '</option>';
                }
                $conn->close();
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="package">Tour Package:</label>
            <select id="package" name="package_id" required>
                <option value="">Select Package</option>
                <?php
                include '../Database/db_connect.php';
                $pkg_sql = "SELECT id, name FROM tour_packages ORDER BY name";
                $pkg_result = $conn->query($pkg_sql);
                while ($pkg = $pkg_result->fetch_assoc()) {
                    echo '<option value="' . $pkg['id'] . '">' . htmlspecialchars($pkg['name']) . '</option>';
                }
                $conn->close();
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price">Price (₹):</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label for="duration">Duration (days):</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
        <div class="form-group">
            <label for="image">Tour Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Tour</button>
            <button type="button" class="btn btn-danger" id="cancel-btn">Cancel</button>
        </div>
    </form>
</div>
