

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Media</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
   
@import url('https://fonts.googleapis.com/css2?family=Anek+Devanagari:wght@500&family=Khand:wght@500;600;700&family=Noto+Sans+Devanagari:wght@100..900&display=swap');

body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    padding: 40px;
}
.container {
    max-width: 520px;
    margin: auto;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
label { font-weight: bold; margin-top: 15px; display: block; }

input, select, button {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
}

input.error, select.error {
    border: 2px solid red;
}

button {
    background: #007bff;
    color: #fff;
    border: none;
    margin-top: 20px;
    cursor: pointer;
}
button:hover { background: #0056b3; }
</style>
</head>
<body>

<div class="container">

<h2>Create Media</h2>

<form id="mediaForm" method="POST" action="{{ route('news.create') }}" enctype="multipart/form-data">
@csrf

<label>Select News catagory</label>
<select id="newscatogary" name="category_id" required>
    <option value="">-- Select Template --</option>
    @foreach ($categories as $category) 
        <option value="{{ $category->id }}"> {{ $category->name }} </option>
    @endforeach
</select>
<label>Select Template</label>
<select id="templateType" name="template_type" required>
    <option value="">-- Select Template --</option>
    @foreach ($templetName as $name) 
        <option value="{{ $name->id }}"> {{ $name->name }} </option>
    @endforeach
    
</select>

<div id="dynamicFields"></div>

<button type="submit">Submit</button>

</form>

</div>

<script>

// create common fields
function commonFields(){
    return `
        <label>Heading</label>
        <input type="text" name="heading">
        <div class="error-text" id="heading_error"></div>

        <label>Description</label>
        <input type="text" name="description">
        <div class="error-text" id="description_error"></div>

        <label>City Name</label>
        <input type="text" name="city">
        <div class="error-text" id="city_error"></div>

        <label>Hashtag</label>
        <input type="text" name="hashtag">
        <div class="error-text" id="hashtag_error"></div>
    `;
}

// load dynamic fields
document.getElementById("templateType").addEventListener("change", function(){

    let type = this.value;
    let fields = commonFields();

    if(type === "2"){
        fields += `
            <label>Upload Image</label>
            <input type="file" name="image">
            <div class="error-text" id="image_error"></div>
        `;
    }

    if(type === "4"){
        fields += `
            <label>Upload Video</label>
            <input type="file" name="video">
            <div class="error-text" id="video_error"></div>
        `;
    }

    document.getElementById("dynamicFields").innerHTML = fields;
});




</script>

</body>
</html>