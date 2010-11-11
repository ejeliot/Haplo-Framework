<p>An example contact form using nonces.</p>

<form method="post" action="/contact/">
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="">
    </div>
    <div>
        <label for="message">Message:</label>
        <textarea rows="10" cols="30" name="message" id="message"></textarea>
    </div>
    <div>
        <input type="submit" name="send" value="Send">
    </div>
</form>