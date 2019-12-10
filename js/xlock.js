/*
* interface access module of post and get through ajax
*/

var access = function(path='', data={})
{
  this.path = path;
  this.data = JSON.stringify(data);
}

access.prototype.get = async function()
{
  let path = this.path;

  if (path.length > 0)
  {
      this.url = path;
  }
  var j = [];
  await $.ajax({
      type: "GET",
      url: this.url,
      contentType: 'application/json',
      dataType: 'json',
      success: function(data)
      {
        j = data;
      }
  });

  this.data = j['data'];
  //console.log(this.data);
}

access.prototype.post = async function()
{
  let path = this.path;
  let data = this.path;

  if (path.length > 0)
  {
      this.url = path;
  }

  var j = await $.ajax({
      type: "POST",
      url: this.url,
      data: this.data,
      contentType: 'application/json',
      dataType: 'json'
  });

  this.data = j['data'];
  //console.log('working');
}


/*
* form handling module
* validation annd submission
*/

var forms = function(targets = {})
{
  //accessibility flag
  this.accessible = true;
  this.targets = targets;
  this.package = {};

  if (Object.keys(targets).length == 0)
  {
    //available input to work with
 
    this.accessible = false;
    
  }
}

forms.prototype.globalMessage = function()
{
  //global alerts
  console.log("forms expects at least one target");
  alert("Invalid use of forms");
}

forms.prototype.reset = function()
{

  if (this.accessible == false)
  {
    this.globalMessage();
  }
  else
  {
    for (let key of Object.keys(this.targets))
    {
      let valType = this.targets[key].validate;
      let view = this.targets[key].viewDock;
      let label = this.targets[key].label;

      document.getElementById(key).value = "";
      document.getElementById(view).value = "";
      this.alert("", "none", key+'Dock', false, true);
    }
    this.alert("", "none", "formMessage", false, true);
  }
}

forms.prototype.validate = function()
{
  let ret = {};
  if (this.accessible == false)
  {
    this.globalMessage();
  }
  else
  {
    let fault = false;
    for (let key of Object.keys(this.targets))
    {
      let val = new valid(key);
      let valType = this.targets[key].validate;
      let view = this.targets[key].viewDock;
      let label = this.targets[key].label;

      if(val.isEmpty())
      {
        fault = true;
        document.getElementById(view).value = "";
        this.alert("*Missing value for "+label, "danger", key+'Dock');
      }
      else
      {
        //number
        if (valType == 'number' && val.isNumber())
        {
          document.getElementById(view).value = val.getValue();
          ret[key] = val.getValue();
          this.alert("", "none", key+'Dock', false, true);
        }
        else if(valType == 'number')
        {
          document.getElementById(view).value = "";
          this.alert("*Invalid Type for "+label+". Must be a Number", "warning", key+'Dock');
        }

        //email
        if (valType == 'email' && val.isEmail())
        {
          document.getElementById(view).value = val.getValue();
          ret[key] = val.getValue();
          this.alert("", "none", key+'Dock', false, true);
        }
        else if(valType == 'email')
        {
          document.getElementById(view).value = "";
          this.alert("*Invalid Type for "+label+". Must be an Email", "warning", key+'Dock');
        }

        //text
        if (valType == 'text' && val.isString())
        {
          document.getElementById(view).value = val.getValue();
          ret[key] = val.getValue();
          this.alert("", "none", key+'Dock', false, true);
        }
        else if(valType == 'text')
        {
          document.getElementById(view).value = "";
          this.alert("*Invalid Type for "+label+". Must be Text", "warning", key+'Dock');
        }

        this.alert("", "none", "formMessage", false, true);
      }
    }

    if (fault == true)
    {
      let msg = "Ensure All issue are resolved before Saving";
      this.alert(msg, "danger", "formMessage", true);
    }
  }

  //packaging values for post
  this.package = ret;
  return ret;
}

forms.prototype.alert = function(message="All is well.", type="info", dock="formMessage", timeOut=false, flat=false)
{
  /*
  * rendering message alerts
  */

  let content = "<div class='alert alert-"+type+"'>"+message+"</div>";
  if(flat == true)
  {
    content = "";
  }
  //console.log(content);
  $('#'+dock).html(content);

  if(timeOut == true)
  {
    //console.log("timeOut to message Box");
    setTimeout(
      function()
      {
        $('#'+dock).html("");
      }, 10000);
  }
}

forms.prototype.save = async function()
{
  let url = "form.php";
  let payLoad = this.package;
  payLoad.op = "save";

  let post = new access(url, payLoad);
  await post.post();
  
  //console.log(post.data);

  this.alert(post.data.global.message, post.data.global.alert, post.data.global.dock, true, false);

}

forms.prototype.remove = async function()
{
  /*
  * switch ids for the package
  */

  let url = "form.php";
  let pack = {};
  pack.op = "remove";

  for (let key of Object.keys(this.targets))
  {
    let anchor = this.targets[key].pack;
    pack[anchor] = this.package[key];
  }

  let post = new access(url, pack);
  await post.post();

  console.log(post.data);

  this.alert(post.data.global.message, post.data.global.alert, post.data.global.dock, true, false);
}



/*
* validator
*/

let valid = function(value = "")
{
  this.proceed = true;

  if (value.length > 0)
  {
    this.value = document.getElementById(value).value;

    /*
    * check if anything exists
    */

    if(this.value.length == 0)
    {
      this.proceed = false;
    }
  }
  else
  {
    this.proceed = false;
  }
}

valid.prototype.isEmpty = function()
{
  if(this.proceed == false)
    return true;
  else
    return false
}

valid.prototype.isNumber = function()
{
  if (this.value > 0)
  {
    return true;
  }
  else
  {
    return false;
  }
}

valid.prototype.isEmail = function()
{
  /*
  * regular expression to check email
  */

  return /(\S+)@(\S+)\.(\S+)/.test(this.value);
}

valid.prototype.isString = function()
{
  let ret = true;

  let buffer = this.value.split(" ");

  for (let value of Object.values(buffer))
  {
    if (/[^A-Za-z]/.test(value) == true)
    {
      ret = false;
    }
  }

  return ret;
}

valid.prototype.getValue = function()
{
  if (this.proceed == true)
  {
    return this.value;
  }
  else
  {
    return false;
  }
}