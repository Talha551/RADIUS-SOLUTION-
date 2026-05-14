<style>
    textarea {
        padding: 10px;
        vertical-align: top;
        width: 100%;
    }
</style>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $("#saveSubscriber").on("submit", function(e) {

            e.preventDefault();
            let isValid = true;

            // Validate First Name - at least 3 characters
            const firstName = $("#fname").val();
            if (firstName.length < 3) {
                isValid = false;
                alert("First Name must be at least 3 characters.");
            }

            // Validate Last Name - at least 3 characters
            const lastName = $("#lname").val();
            if (lastName.length < 3) {
                isValid = false;
                alert("Last Name must be at least 3 characters.");
            }

            // Validate Username - at least 6 characters, no spaces
            //const username = $("#user").val();
            //const usernamePattern = /^[a-z\d@]{6,}$/;
            //if (!usernamePattern.test(username)) {
            //    isValid = false;
            //    alert("Username must be at least 6 characters and only contain @ without spaces.");
            //}

            // Validate Username - at least 6 characters, no spaces
            const username = $("#user").val();
            const usernamePattern = /^[a-z\d@]{6,}$/;
            if (!usernamePattern.test(username)) {
                isValid = false;
                alert("Username must be at least 6 characters and only contain @ without spaces.");
            } else {
                // AJAX call to check if the username already exists
                $.ajax({
                    url: "<?php echo base_url(); ?>subscribers/checkUserExist",
                    type: "POST",
                    data: { userId: username },
                    async: false, // Make sure this completes before proceeding
                    success: function(response) {
                        console.log(response);

                        if (response == "exists") {
                            isValid = false;
                            alert("Username already exists. Please choose another.");
                        }
                        usernameChecked = true;
                    },
                    error: function() {
                        isValid = false;
                        alert("Error checking username.");
                        usernameChecked = true;
                    }
                });
            }

            // Validate Valid ID - between 13 and 20 characters
            const validId = $("#validid").val();
            if (validId.length < 13 || validId.length > 20) {
                isValid = false;
                alert("Valid ID must be between 13 and 20 characters.");
            }

            // Validate Password - at least 4 characters, no spaces
            const password = $("#password1").val();
            const passwordPattern = /^[A-Za-z\d]{4,}$/;
            if (!passwordPattern.test(password)) {
                isValid = false;
                alert("Password must be at least 4 characters and contain a number without spaces.");
            }

            // Validate matching passwords
            const repeatPassword = $("#password2").val();
            console.log(password + "-");
            console.log(repeatPassword);
            if (repeatPassword !== password) {
                isValid = false;
                alert("Passwords do not match.");
            }

            // Validate Contact Number - between 12 and 20 characters
            const mobile = $("#mobile").val();
            if (mobile.length < 12 || mobile.length > 20) {
                isValid = false;
                alert("Contact number must be between 12 and 20 characters.");
            }

            //if (!isValid) {
            //    e.preventDefault(); // Prevent form submission if validation fails
            //}

            //return isValid;

            if (isValid) {
                alert("New user created successfully.");

                $("#saveSubscriber").unbind('submit').submit(); // Unbind the submit event to allow form submission
            }
        });

        // Show/Hide Password Logic
        $("#eye").hide(); // Initially hide the 'eye' icon

        function hideshow() {
            const passwordField = $("#password1");
            if (passwordField.attr("type") === "password") {
                passwordField.attr("type", "text"); // Show the password
                $("#slash").hide(); // Hide the 'eye-slash' icon
                $("#eye").show(); // Show the 'eye' icon
            } else {
                passwordField.attr("type", "password"); // Hide the password
                $("#slash").show(); // Show the 'eye-slash' icon
                $("#eye").hide(); // Hide the 'eye' icon
            }
        }

        $("#eye").click(hideshow); // Toggle visibility on click
    });
</script>

<div class="main-container">

    <div class="page-header">
        <div class="row">


            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>Subscruber</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo base_url('Subscribers/subscribersListView'); ?>">Subscribers</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Add Subscriber
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <div class="dropdown">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        Users
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Export List</a>
                        <a class="dropdown-item" href="#">Policies</a>
                        <a class="dropdown-item" href="#">View Assets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pd-20 card-box mb-30">
        <!-- page content -->
        <div class="right_col" role="main">
            <div class="">
                <div class="clearfix">
                    <div class="pull-left">
                        <p class="mb-30">New PPPoE User Contract Details</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="x_panel">

                            <div class="container mt-5">

                                <div class="x_content">
                                    <form id="saveSubscriber" class="" action="<?php echo base_url() ?>subscribers/saveSubscriber" method="post">

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Owner<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <select class="form-control required" id="manager" name="manager">

                                                    <?php
                                                    if (!empty($managers)) {
                                                    ?> <option value="0">Select Owner</option> <?php
                                                                                                foreach ($managers as $rl) {
                                                                                                ?>
                                                            <option value="<?php echo $rl->managername ?>" <?php if ($rl->managername == set_value('managername')) {
                                                                                                                echo "selected=selected";
                                                                                                            } ?>><?php echo $rl->managername ?></option>
                                                        <?php
                                                                                                }
                                                                                            } else {
                                                        ?>
                                                        <option value="<?php echo $this->session->userdata('name') ?>" selected=selected><?php echo $this->session->userdata('name') ?></option>
                                                    <?php
                                                                                            }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">First Name<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" name="fname" id="fname" required="required" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Last Name<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" name="lname" id="lname" required="required" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Username<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" id="user" name="user" type="text" pattern="^[a-z\d@]{6,}$" title="Minimum 6 Characters only @ allowed with username, without spaces" required="required" autocomplete="nope" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Valid ID <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" type="number" class='number' name="validid" id="validid" required='required' autocomplete="nope">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Password<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" type="password" id="password1" name="password1" autocomplete="nope" required />

                                                <span style="position: absolute;right:15px;top:7px;" onclick="hideshow()">
                                                    <i id="slash" class="fa fa-eye-slash"></i>
                                                    <i id="eye" class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Repeat password<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" type="password" id="password2" name="password2" required='required' autocomplete="nope" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Contact Number<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control" type="tel" class='tel' name="mobile" id="mobile" autocomplete="nope" required='required' />
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-md-3 col-sm-3  label-align">Remarks</label>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea name='message'></textarea>
                                            </div>
                                        </div>


                                        <div class="ln_solid"></div>
                                        <div class="form-group row">
                                            <div class="col-md-9 offset-md-3">
                                                <button type="submit" class="btn btn-primary">Cancel</button>
                                                <button type="submit" class="btn btn-success">Submit</button>
                                            </div>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
</div>


<!-- Javascript functions	-->
<script>
    function hideshow() {
        var password = document.getElementById("password1");
        var slash = document.getElementById("slash");
        var eye = document.getElementById("eye");

        if (password.type === 'password') {
            password.type = "word";
            slash.style.display = "block";
            eye.style.display = "none";
        } else {
            password.type = "password";
            slash.style.display = "none";
            eye.style.display = "block";
        }

    }
</script>



</body>

</html>