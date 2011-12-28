<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <meta name="description" content="<?php echo $metaDesc; ?>">
    <meta name="keywords" content="<?php echo $metaKeywords; ?>">
</head>

<body class="<?php echo str_replace(array('/', '_'), '-', $section); ?>">
    <?php echo $content; ?>
</body>

</html>