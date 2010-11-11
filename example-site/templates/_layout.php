<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $metaTitle; ?></title>
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <meta name="keywords" content="<?php echo $metaKeywords; ?>">
    <style type="text/css">
        .haplo { border: 2px solid #ff9900; background: #eee; padding: 10px; margin: 10px 0; }
        .haplo * { padding: 0; margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 14px; }
        .haplo h1, .haplo p, .haplo ul { margin: 5px; }
        .haplo h2 { margin: 20px 5px; }
        .haplo h1 { font-size: 20px; }
        .haplo ul { margin: 10px 40px; }
        .haplo .copyright { border-top: 1px solid #ccc; padding-top: 10px; margin: 20px 5px 0 5px; font-size: 12px; }
    </style>
</head>

<body>
    <div class="haplo">
        <?php echo $content; ?>
        <p class="copyright">Haplo Framework - Copyright &copy; 2010 Brightfish Software/Ed Eliot. BSD License (see LICENSE for details).</p>
    </div>
</body>

</html>