<!DOCTYPE html>
<html>
<head>
  <style>
  textarea { height:45px; }
  </style>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
	
<table>
<form>
<table id="exampleTable" border="1" cellpadding="10" align="center">

    <tr>
        <th>
            Element Type
        </th>
        <th>
            Element
        </th>

     </tr
    ><tr>
        <td>
            <input type="button" value="Input Button"/>
        </td>

    </tr>
    <tr>
        <td>
            <input type="checkbox" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="file" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="hidden" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="image" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="password" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="radio" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="reset" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="submit" />
        </td>

    </tr>
    <tr>
        <td>
            <input type="text" />
        </td>

    </tr>
    <tr>
        <td>
            <select><option>Option</option></select>
        </td>

    </tr>
    <tr>
        <td>
            <textarea></textarea>
        </td>
    </tr>

    <tr>
        <td>
            <button>Button</button>
        </td>
    </tr>
    <tr>
        <td>
            <button type="submit">Button type="submit"</button>
        </td>
    </tr>

</table>
</form>
<div id="result"></div>

<script>
    var submitEl = $("td :submit")
      .parent('td')
      .css({background:"yellow", border:"3px red solid"})
    .end();
    
    $('#result').text('jQuery matched ' + submitEl.length + ' elements.');

    // so it won't submit
    $("form").submit(function () { return false; });
    
    // Extra JS to make the HTML easier to edit (None of this is relevant to the ':submit' selector
    $('#exampleTable').find('td').each(function(i, el) {
        var inputEl = $(el).children(),
            inputType = inputEl.attr('type') ? ' type="' + inputEl.attr('type') + '"' : '';
        $(el).before('<td>' + inputEl[0].nodeName + inputType + '</td>');
    })
    

</script>
</body>
</html>