<h1>An example of using using nonces.</h1>

<?php if (!empty($message)): ?>
    <?php echo $message; ?>
<?php endif; ?>

<table>
    <thead>
        <th>Product</th>
        <th>Description</th>
        <th>Actions</th>
    </thead>
    <tbody>
        <?php for ($i = 1; $i <= 30; $i++): ?>
            <tr>
                <td>Product <?php echo $i; ?></td>
                <td>Description of product <?php echo $i; ?></th>
                <td>
                    <form method="post" action="/nonce-example/">
                        <div>
                            <input type="hidden" name="id" value="<?php echo $i; ?>">
                            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
                            <input type="submit" name="delete" value="Delete">
                        </div>
                    </form>
                </td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>