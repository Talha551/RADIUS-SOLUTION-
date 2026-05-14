<!-- This is inside recharge_form.php -->
<form>
    <input type="text" name="username" value="<?php echo $username; ?>">
    <!-- Form fields for the recharge action -->
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="text" class="form-control" id="amount" name="amount">
    </div>
    <button type="submit" class="btn btn-primary">Submit Recharge</button>
</form>