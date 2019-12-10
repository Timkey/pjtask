async function validate()
{
	/*
	*	{'id' : {'validate':'', 'viewDock':'', 'label':'', }, {}, }
	*	id => input tracker
	*	validate => input validation type
	*	viewDock => alternate view and delete id
	*	label => input tag
	*/

	let target = {};
	target.name  = {"validate":"text", "viewDock":"nameD", "label":"Name"};
	target.email = {"validate":"email", "viewDock":"emailD", "label":"Email"};
	target.phone = {"validate":"number", "viewDock":"phoneD", "label":"Phone Number"};

	let validator = new forms(target);
	validator.validate();

}

async function newContact()
{
	/*
	*	{'id' : {'validate':'', 'viewDock':'', 'label':'', }, {}, }
	*	id => input tracker
	*	validate => input validation type
	*	viewDock => alternate view and delete id
	*	label => input tag
	*/

	let target = {};
	target.name  = {"validate":"text", "viewDock":"nameD", "label":"Name"};
	target.email = {"validate":"email", "viewDock":"emailD", "label":"Email"};
	target.phone = {"validate":"number", "viewDock":"phoneD", "label":"Phone Number"};

	let form = new forms(target);
	form.reset();

}

async function save()
{
	/*
	*	{'id' : {'validate':'', 'viewDock':'', 'label':'', }, {}, }
	*	id => input tracker
	*	validate => input validation type
	*	viewDock => alternate view and delete id
	*	label => input tag
	*/

	let target = {};
	target.name  = {"validate":"text", "viewDock":"nameD", "label":"Name"};
	target.email = {"validate":"email", "viewDock":"emailD", "label":"Email"};
	target.phone = {"validate":"number", "viewDock":"phoneD", "label":"Phone Number"};

	let validator = new forms(target);
	let check = validator.validate();

	if (Object.keys(check).length == Object.keys(target).length)
	{
		validator.save();
	}

}

async function remove()
{
	/*
	*	{'id' : {'validate':'', 'viewDock':'', 'label':'', }, {}, }
	*	id => input tracker
	*	validate => input validation type
	*	viewDock => alternate view and delete id
	*	label => input tag
	*/

	let target = {};
	target.nameD  = {"validate":"text", "viewDock":"nameD", "label":"Name", "pack":"name"};
	target.emailD = {"validate":"email", "viewDock":"emailD", "label":"Email", "pack":"email"};
	target.phoneD = {"validate":"number", "viewDock":"phoneD", "label":"Phone Number", "pack":"phone"};

	let validator = new forms(target);
	let check = validator.validate();

	if (Object.keys(check).length == Object.keys(target).length)
	{
		validator.remove();
	}

}