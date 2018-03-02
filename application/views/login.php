<?php
/**
 * User: sbraun
 * Date: 28.02.18
 * Time: 10:25
 * @source https://codepen.io/colorlib/pen/rxddKy
 */
?>
<div class="login-page">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Roboto:300);

        .login-page {
            width: 360px;
            padding: 8% 0 0;
            margin: auto;
        }

        .login-page .form {
            position: relative;
            z-index: 1;
            background: #FFFFFF;
            max-width: 360px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
        }

        .login-page .form input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f2f2f2;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .login-page .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: #4CAF50;
            width: 100%;
            border: 0;
            margin: 0 0 15px 0;
            padding: 15px;
            color: #FFFFFF;
            font-size: 14px;
            -webkit-transition: all 0.3 ease;
            transition: all 0.3 ease;
            cursor: pointer;
        }

        .login-page .form button:hover,
        .login-page .form button:active,
        .login-page .form button:focus {
            background: #43A047;
        }

        .login-page .form .message {
            /*margin: 15px 0 0 0;*/
            margin: 5px 0 0 0;
            /*margin: 0 0 15px 0;*/
            color: #b3b3b3;
            font-size: 14px;
            font-family: "Open Sans", sans-serif;
        }

        .login-page .form .message a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-page .form .register-form {
            display: none;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 300px;
            margin: 0 auto;
        }

        .container:before, .container:after {
            content: "";
            display: block;
            clear: both;
        }

        .container .info {
            margin: 50px auto;
            text-align: center;
        }

        .container .info h1 {
            margin: 0 0 15px;
            padding: 0;
            font-size: 36px;
            font-weight: 300;
            color: #1a1a1a;
        }

        .container .info span {
            color: #4d4d4d;
            font-size: 12px;
        }

        .container .info span a {
            color: #000000;
            text-decoration: none;
        }

        .container .info span .fa {
            color: #EF3B3A;
        }

        body.login-form {
            background: #76b852; /* fallback for old browsers */
            background: -webkit-linear-gradient(right, #76b852, #8DC26F);
            background: -moz-linear-gradient(right, #76b852, #8DC26F);
            background: -o-linear-gradient(right, #76b852, #8DC26F);
            background: linear-gradient(to left, #76b852, #8DC26F);
            font-family: "Roboto", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    <div class="form">
        <form class="register-form" action="<?= $form_url ?>" method="post">
            <input name="login[name]"  type="text" placeholder="name"/>
            <input name="login[password]" type="password" placeholder="password"/>
            <input name="login[email]" type="email" placeholder="email address"/>
            <button name="login[create]" value="1">create</button>
            <button name="login[google]" value="2" onclick="location.href = '<?= $google_url ?>';return false;">create with google</button>
            <p class="message">Already registered? <a href="#">Sign In</a></p>
        </form>
        <form class="login-form" action="<?= $form_url ?>" method="post">
            <input name="login[email]" type="email" placeholder="email address"/>
            <!--            <input name="login[username]" type="text" placeholder="username"/>-->
            <input name="login[password]" type="password" placeholder="password"/>
            <button name="login[login]" value="1">login</button>
            <button name="login[google]" value="2" onclick="location.href = '<?= $google_url ?>';return false;">login with google</button>
            <p class="message">Not registered? <a href="#">Create an account</a></p>
        </form>
    </div>
    <script>
        $('.login-page .message a').click(function () {
            $('.login-page form').animate({height: "toggle", opacity: "toggle"}, "slow");
        });
    </script>
</div>