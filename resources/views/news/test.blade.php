<!DOCTYPE html>
<html>
<head>
    <title>Category Form</title>
</head>
<body>

<form action="{{ route('category.store') }}" method="POST">
    @csrf

    <input type="text" name="name" placeholder="Name">
    <br><br>

    <input type="text" name="slug" placeholder="Slug">
    <br><br>

    <button type="submit">Submit</button>
</form>

</body>
</html>
