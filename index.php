<?php
require_once ('recaptchalib.php');
require_once ('smtpMailer.php');


$publickey = "6LeJNL8fAAAAABKxbTW2tgpCma-atxUrN0fs1aL3";
$privatekey = "";
$reCaptcha = new ReCaptcha($privatekey);

$successMessage = '';
$resp_captcha = null;

$error_captcha = null;
$errors = array();
$values = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $response = null;
    if (isset($_POST['g-recaptcha-response']))
    {
        $response = $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
        if ($response == null || !$response->success)
        {
            $errors['captcha'] = 'please check captcha to verify human';
        }
    }
    foreach ($_POST as $key => $value)
    {
        $values[$key] = trim(stripslashes($value)); 
    }
    if (check_input($values['yourname']) == false)
    {
        $errors['yourname'] = 'Enter your name!';
    }
    if (check_input($values['subject']) == false)
    {
        $errors['subject'] = 'please enter subject!';
    }

    
    if (check_input($values['email']) == false)
    {
        $errors['email'] = 'Please enter your email address.';
    }
    else if (!preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/', $values['email']))
    {
        $errors['email'] = 'Invalid email address format.';
    }

    if (check_input($values['phone']) == false)
    {
        $errors['phone'] = 'Please enter your phone number.';
    }
    else if (!preg_match('/^[0-9]{10}+$/', $values['phone']))
    {
        $errors['phone'] = 'Invalid phone number';
    }


    if (check_input($values['comments']) == false)
    {
        $errors['comments'] = 'Write your comments!';
    }

    if (sizeof($errors) == 0)
    {
      


        $mail = new SMTPMailer();

        $mail->addTo('admin@gmail.com');

        $mail->Subject($values["subject"]);

      
        $body=
         " name :".$values["yourname"] ."\n".
         " phone :".$values["phone"] ."\n".
         " email :".$values["email"] ."\n".
         " message :".$values["comments"] ."\n";
         
        $mail->Body($body );

        if ($mail->Send()){
            $successMessage = "Your message has sent. Thank you !";
            $values = array(); 
        }



    
    }
}
function check_input($input)
{
    if (strlen($input) == 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}
?>

<!doctype html>
<html>

<body>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <div class="container">
        <div class="col-md-5">
            <div class="form-area">


                <?php if($successMessage){ ?>
                    <div class="alert alert-success" role="alert"> <?php echo $successMessage; ?></div> 
                <?php }?>

                <form role="form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" id="form-contact">
                    <br style="clear:both">
                    <h3 style="margin-bottom: 25px; text-align: center;">Contact Form</h3>
                    
                    <div class="form-group">
                        <p>Name: <input type="text" class="form-control" name="yourname"
                                value="<?php echo htmlspecialchars(@$values['yourname']) ?>" /></P>
                        <?php if (isset($errors['yourname']))
                        { ?>
                        <div style="color: red;">
                            <?php echo $errors['yourname']; ?>
                        </div>
                        <?php
                        } ?>
                    </div>
                    <div class="form-group">
                        <P>Email: <input type="text" class="form-control" name="email"
                                value="<?php echo htmlspecialchars(@$values['email']) ?>" /></p>
                        <?php if (isset($errors['email']))
                        { ?>
                        <div style="color: red;">
                            <?php echo $errors['email']; ?>
                        </div>
                        <?php
                        } ?>
                    </div>
                    <div class="form-group">
                        <p>Phone: <input class="form-control" type="text" name="phone"
                                value="<?php echo htmlspecialchars(@$values['phone']) ?>" /></p><br />
                        <?php if (isset($errors['phone']))
                        { ?>
                        <span style="color: red;">
                            <?php echo $errors['phone']; ?>
                        </span>
                        <?php
                        } ?>
                    </div>
                    <div class="form-group">
                        <p>Subject: <input class="form-control" type="text" name="subject"
                                value="<?php echo htmlspecialchars(@$values['subject']) ?>" /></p>
                        <?php if (isset($errors['subject']))
                        { ?>
                        <span style="color: red;">
                            <?php echo $errors['subject']; ?>
                        </span>
                        <?php
                        } ?>
                    </div>
                    <div class="form-group">
                        <p>Comments:
                            <?php if (isset($errors['comments']))
                            { ?>
                            <span style="color: red;">
                                <?php echo $errors['comments']; ?>
                            </span>
                            <?php
                            } ?>
                            <br />
                            <textarea class="form-control" name="comments" rows="10" cols="50" style="width: 100%;">
                                <?php echo htmlspecialchars(@$values['comments']) ?>
                            </textarea>
                        </p>
                    </div>
                    <div>
                        <div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
                        <?php if (isset($errors['captcha']))
                        { ?>
                        <span style="color: red;">
                            <?php echo $errors['captcha']; ?>
                        </span>
                        <?php  } ?>
                    </div>
                    <div>
                    <button form="form-contact"  type="submit" id="submit" name="submit" class="btn btn-primary pull-right">Submit
                        Form</button>
                    </div>
                </form>
        
            </div>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js"></script>
</body>

</html>