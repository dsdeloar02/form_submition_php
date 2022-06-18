<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


<h2> PhP Class 13 - Super Global Variables </h2>


<?php

    $errors = [];

    try {
        if (isset($_POST['contact_form'])) {

            // Validation ......

            if (empty($_POST['name'])) {
                $errors['name'] = "Please give a valid name .";
            }
            if (empty($_POST['subject'])) {
                $errors['subject'] = "Please give subject .";
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Please give a valid email.";
            }

            if (empty($_POST['temp_password'])) {
                $errors['temp_password'] = "Please give temp_password .";
            }

            // *** Upload file,,,,,
    
            //move_uploaded_file(string $from, string $to):
            // $new_path = "uploads/" . basename($_FILES["attachment"]["name"]);
            

            $file_name = null;
            if (empty($_FILES['attachment']['name'])) {
                $errors['file'] = "Please give valid file .";
               // move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/" . basename($_FILES["attachment"]["name"]));
            } else{
                
                 // If this request falls under any of them, treat it invalid.
                 if (
                    !isset($_FILES['attachment']['error']) ||
                    is_array($_FILES['attachment']['error'])
                ) {
                    throw new RuntimeException('Invalid parameters');
                }

                // Check file size exceeded or not
                $file_size = !empty($_FILES['attachment']['size']) ? intval($_FILES['attachment']['size']) : 0 ;
                $upto_supported_size = 1 * 1024 * 1000;

                if ($file_size > $upto_supported_size) {
                    $errors['attachment'] = "File size exceeded. Max upload limit is 5MB";
                    throw new Exception($errors['attachment'], 1);
                }

                // Mime type of file
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $file_ext = array_search(
                    $finfo->file($_FILES['attachment']['tmp_name']),
                    array(
                    'jpg' => 'image/jpg',
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'gif' => 'image/bnf',
                    'pdf' => 'application/pdf'
                ),
                    true
                );
                if (false === $file_ext) {
                    $errors['attachment'] = "File should be a valid image or PDF file";
                    throw new Exception($errors['attachment'], 1);
                }


                // Sanitize file name and make a new name
                $file_name = 'devsenv-' . time() . '.' . $file_ext;

                // Upload file
                if (!move_uploaded_file($_FILES['attachment']['tmp_name'], "uploads/" . $file_name)) {
                    $errors['attachment'] = "File uploads fail, please try again.";
                    // throw new Exception($errors['attachment']);
                }
            }
            
        }
    } catch (Exception $e) {
        $errors['global']= $e->getMessage();
    }




    if (count($errors) > 0) {
        $err_global = "Failed to submit form. Please try again.";
    
        if (isset($errors['global'])) {
            $err_global .= ' Error: ' .$errors['global'];
        }
    
        $errors['global'] = $err_global;
    } else {
        if (isset($_POST['contact_form'])) {
            // Sanitization
            $insert_data = [
                'name'          => htmlspecialchars($_POST['name']),
                'email'         => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
                'subject'       => htmlspecialchars($_POST['subject']),
                'temp_password' => htmlspecialchars($_POST['temp_password']),
                'attachment'    => $file_name
            ];
            var_dump($insert_data);
    
            // Insert into Database
            // DB::insert('contact_us', $insert_data);
        }
    }
?>

<form method = "POST" action= "<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" style='width:60%; margin:50px auto; border:1px solid green; padding:10px; margin-top:10px;'>

<?php
    if (!empty($_POST['name']) && !empty($_POST['subject']) && !empty($_POST['temp_password'])) {
        echo "<h3> Your Form is Submitted ! </h3>";
    }
?>
    <label for="name">Name</label>
     <br> 
    <input type="text" id="name" name="name" style="width:100%; height:40px; border:1px solid #ccc " ><br>
    <span style="color:red"> <?php echo  !empty($errors['name']) ? $errors['name']:" "?> </span>
    <br>
    <label for="name">Subject</label> <br>
    <input type="text" id="subject" name="subject" style="width:100%; height:40px; border:1px solid #ccc "><br>
    <span style="color:red"> <?php echo  !empty($errors['subject']) ? $errors['subject']:" "?>  </span>
    <br>
    <label for="email">Email</label> <br>
    <input type="email" id="email" name="email" style="width:100%; height:40px; border:1px solid #ccc " ><br>
    <span style="color:red"> <?php echo  !empty($errors['email']) ? $errors['email']:" "?>  </span>
    <br>
    <label for="name">Temp Password</label> <br>
    <input type="password" id="temp_password" name="temp_password" style="width:100%; height:40px; border:1px solid #ccc " ><br>
    <span style="color:red"> <?php echo !empty($errors['temp_password']) ? $errors['temp_password']:" "?>  </span>
    <br>
    <label for="name">Attachment (PDF or Image ) </label> <br>
    <input type="file" id="attachment" name="attachment" style="width:100%; height:40px; border:1px solid #ccc "><br>
    <span style="color:red"> <?php echo !empty($errors['attachment']) ? $errors['attachment']:" "?>  </span> <br>
    <br>


    <input type="submit" name="contact_form" style= " width:300px; height:40px; text-align:center; background:green; color:white; ">
</form>
    
</body>
</html>

