async function search()
{
	var input = document.getElementById('searchBar').value;

	if (input.length > 1)
	{
		/*
		*	post method
		let url = "api.php";
		let payLoad = {};
		payLoad['searchParam'] = input;
		let loc = new access(url, payLoad);

		await loc.post();
		*/

		let url = "api.php?searchParam="+input;
		let loc = new access(path=url);
		let found = false;

		await loc.get();
		let data = await loc.data;
		let buffer = "<table class='table table-striped'><tbody>";


		for (let value of Object.values(data))
		{
			found = true;
			buffer += "<tr><td>"+value+"</td></tr>";
		}

		if (found == false)
		{
			buffer += "<tr><td>No Results Found</td></tr>";
		}

		buffer += "</tbody></table>";

		$('#searchDock').html(buffer);
	}
	else
	{
		$('#searchDock').html("<div class='alert alert-info'>Your Search must contain at least 2 characters</div>");
	}
}