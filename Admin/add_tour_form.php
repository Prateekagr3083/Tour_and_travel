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
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        <div class="form-group">
            <label for="price">Price (₹):</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label for="duration">Duration (days):</label>
            <input type="number" id="duration" name="duration" min="1" required>
        </div>
       <!-- <div class="form-group">
            <label for="destination">Destination:</label>
            <input type="text" id="destination" name="location" required>
        </div> -->
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