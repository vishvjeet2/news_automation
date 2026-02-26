<!DOCTYPE html>
<html>
<head>
    <title>Add Template</title>
</head>
<body>

<h2>Add Template</h2>

<form action="{{ route('news.templates.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Type</label><br>
    <select name="type">
        <option value="image">Image</option>
        <option value="video">Video</option>
    </select>
    <br><br>

    <label>Select File</label><br>
    <input type="file" name="file" required>
    <br><br>

    <button type="submit">Save</button>
</form>

</body>
</html>
