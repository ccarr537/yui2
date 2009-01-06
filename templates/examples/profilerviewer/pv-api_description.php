<h2 class="first">Making Use of ProfilerViewer's API</h2>

<p>This example points to how you can use ProfilerViewer's provided API to create profiling experiences tailored to your environment.</p>

<p>This example has the following dependencies:</p>

<textarea name="code" class="HTML" cols="60" rows="1"><link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/container/assets/skins/sam/container.css"> 
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/menu/assets/skins/sam/menu.css"> 
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/profilerviewer/assets/skins/sam/profilerviewer.css">

<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/utilities/utilities.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/element/element-beta-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/container/container-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/menu/menu.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/yuiloader/yuiloader-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/profiler/profiler-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/profilerviewer/profilerviewer-beta-min.js"></script></textarea>


<p>Here are some of the key features of this example:</p>

<h3>1.  Thorough profiling of the YUI Menu Control</h3>

<p>Generally, a thorough profiling of a component like Menu requires careful thought and analysis.  In this case, we want a picture of how Menu's own functions perform and also of how it makes use of other YUI components like the <a href="http://developer.yahoo.com/yui/dom/">Dom Collection</a> and the <a href="http://developer.yahoo.com/yui/event/">Event Utility</a>.  To do this, we need to profile the constructors of the main Menu classes and use <a href="http://developer.yahoo.com/yui/profiler/">Profiler</a>'s <code>registerObject</code> method to profile the static classes of Dom and Event.</p>

<textarea name="code" class="JScript" cols="60" rows="1">// To fully profile the Menu, we want to register all the Menu
// and MenuItem constructors involved in Menu creation; for the
// purposes of this example, we'll also profile Menu's use
// of core YUI components like the Dom Collection and the
// Event Utility:
YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Menu");
YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.MenuItem");
YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.MenuManager");
YAHOO.tool.Profiler.registerConstructor("YAHOO.util.Config");
YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Module");
YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Overlay");
YAHOO.tool.Profiler.registerObject("YAHOO.util.Dom", true);
YAHOO.tool.Profiler.registerObject("YAHOO.util.Event", true);</textarea>

<h3>2.  Using ProfilerViewer's configuration attributes</h3>

<p>In instantiating ProfilerViewer, we'll use configuration attributes to:</p>
<ol>
  <li>Set the base directory for YUI files (so that some files can be loaded only when needed) <strong>(line 3 below)</strong>;</li>
  <li>Set the path to the YUI Charts Control .swf file relative to the page being viewed <strong>(line 21 below)</strong>;</li>
  <li>Filter the Profiler's output, showing in the table only functions that have been called at least once <strong>(lines 11-13 below)</strong>;</li>
  <li>Set the number of functions profiled visually in the chart <strong>(line 19 below)</strong>;</li>
  <li>Set the height of the console's DataTable component <strong>(line 20 below)</strong>.</li>
</ol>

<textarea name="code" class="JScript" cols="60" rows="1">// Instantiate ProfilerViewer, using its API to customize it a bit:
var pv = new YAHOO.widget.ProfilerViewer("profiler", {
    base: "http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/",
    visible: false, //default is false; this means only the
                    //ProfilerViewer launchbar will be displayed
                    //on initial render, and the rest of the console
                    //(including the DataTable and Chart) won't 
                    //be loaded and rendered until they're requested.
    //here, we're going to filter the displayed functions
    //and only show those that have been called at least once:
    filter: function(o) {
        return (o.calls > 0);
    },
    showChart: true, //default is true
    //the chart can be hidden entirely by setting showChart to
    //false; you can also control the number of functions
    //measured in the chart to expand or reduce the real estate
    //it takes up on the page:
    maxChartFunctions:8,
    tableHeight:"25em", //default: 15em
    swfUrl:"http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/charts/assets/charts.swf"
});</textarea>

<h3>3.  Use one of ProfilerViewer's "interesting moments" (custom events) to further customize the interaction</h3>

<p>ProfilerViewer exposes a few custom events (like when the console first renders and when a data-refresh is requested; you can respond to these by subscribing to the events.  Here, we'll use a different class of custom event: one that fires automatically in response to an attribute change.</p>

<p>ProfilerViewer has an attribute called <code>visible</code> that is toggled when the console is minimized/maximized.  We'll subscribe to that event here.  When the console is minimizing, we'll make it narrower (300px wide) so that it's more compact and out of the way.  When <code>visible</code> is changed back to true (ie, when the console is maximized), we will reset the width of the console to 950px to reveal all of the profiling data.</p>

<textarea name="code" class="JScript" cols="60" rows="1">//You can subscribe to "interesting moments" in the ProfilerViewer
//just as you can with any other YUI component.  Here, we'll use
//the visibleChange event that accompanies any change to the PV
//console's "visible" attribute.  When made visible, we'll expand
//the console to full width; when it's minimized, we'll reduce the
//width of the launcher so that it takes up less screen real
//estate:
pv.subscribe("visibleChange", function(o) {

            //"this" is the ProfilerViewer instance;
            //this.get("element") is the top-level node containing
            //the ProfilerViewer console. 
            var el = this.get("element");
            
            //In this handler, the "visible" config property is
            //changing.  If the new value is "true", the console
            //is becoming visible, so we'll make it wide.  If the
            //new value is false, we'll make the launch bar skinny.
            var width = (o.newValue) ? "950px" : "300px";
            YAHOO.util.Dom.setStyle(el, "width", width);
});</textarea>

<h3>4.  Use the <code>getHeadEl()</code> method to provide a drag handle for the draggable console</h3>

<p>To help keep the ProfilerViewer console out of the way, we'll make it draggable via the header bar.  To do this, we need access to the ProfilerViewer's header element.  ProfilerViewer's API gives you access to a number of key elements in the console's DOM; in this case, we'll use the <code>getHeadEl()</code> method to specify the header bar as the drag handle for the console.</p>

<textarea name="code" class="JScript" cols="60" rows="1">//Here, we'll use Drag and Drop to make the ProfilerViewer
//console draggable.	
var DD = new YAHOO.util.DD("profiler");

//ProfilerViewer's API gives you access to the key container
//elements in the console.  Here we'll use access to the
//header element to give it an ID and make it a drag handle.
pv.getHeadEl().id = "profilerHead";
DD.setHandleElId("profilerHead");

//The Buttons in the head should not be valid drag handles; 
//they are comprised of anchor elements, which DD allows us
//to disclude as handles.
DD.addInvalidHandleType("a");

//Drag and Drop performs better when you use the dragOnly
//setting for elements that can be moved but that don't
//have any DD interactions with other page elements:
DD.dragOnly = true;</textarea>

<h3>Full source code</h3>

<p>The full JavaScript source code for this example is as follows:</p>

<textarea name="code" class="JScript" cols="60" rows="1">// Instantiate and render the menu when it is available in the DOM
YAHOO.util.Event.onContentReady("productsandservices", function () {

    // To fully profile the Menu, we want to register all the Menu
    // and MenuItem constructors involved in Menu creation; for the
    // purposes of this example, we'll also profile Menu's use
    // of core YUI components like the Dom Collection and the
    // Event Utility:
    YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Menu");
    YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.MenuItem");
    YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.MenuManager");
    YAHOO.tool.Profiler.registerConstructor("YAHOO.util.Config");
    YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Module");
    YAHOO.tool.Profiler.registerConstructor("YAHOO.widget.Overlay");
    YAHOO.tool.Profiler.registerObject("YAHOO.util.Dom", true);
    YAHOO.tool.Profiler.registerObject("YAHOO.util.Event", true);
    
    // Instantiate ProfilerViewer, using its API to customize it a bit:
    var pv = new YAHOO.widget.ProfilerViewer("profiler", {
        base: "http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/",
        visible: false, //default is false; this means only the
                        //ProfilerViewer launchbar will be displayed
                        //on initial render, and the rest of the console
                        //(including the DataTable and Chart) won't 
                        //be loaded and rendered until they're requested.
        //here, we're going to filter the displayed functions
        //and only show those that have been called at least once:
        filter: function(o) {
            return (o.calls > 0);
        },
        showChart: true, //default is true
        //the chart can be hidden entirely by setting showChart to
        //false; you can also control the number of functions
        //measured in the chart to expand or reduce the real estate
        //it takes up on the page:
        maxChartFunctions:8,
        tableHeight:"25em", //default: 15em
        swfUrl:"http://yui.yahooapis.com/<?php echo $yuiCurrentVersion;?>/build/charts/assets/charts.swf"
    });
            
    //You can subscribe to "interesting moments" in the ProfilerViewer
    //just as you can with any other YUI component.  Here, we'll use
    //the visibleChange event that accompanies any change to the PV
    //console's "visible" attribute.  When made visible, we'll expand
    //the console to full width; when it's minimized, we'll reduce the
    //width of the launcher so that it takes up less screen real
    //estate:
    pv.subscribe("visibleChange", function(o) {
    
                //"this" is the ProfilerViewer instance;
                //this.get("element") is the top-level node containing
                //the ProfilerViewer console. 
                var el = this.get("element");
                
                //In this handler, the "visible" config property is
                //changing.  If the new value is "true", the console
                //is becoming visible, so we'll make it wide.  If the
                //new value is false, we'll make the launch bar skinny.
                var width = (o.newValue) ? "950px" : "300px";
                YAHOO.util.Dom.setStyle(el, "width", width);
    });
    
    //Here, we'll use Drag and Drop to make the ProfilerViewer
    //console draggable.	
    var DD = new YAHOO.util.DD("profiler");

    //ProfilerViewer's API gives you access to the key container
    //elements in the console.  Here we'll use access to the
    //header element to make it a drag handle.
    pv.getHeadEl().id = "profilerHead";
    DD.setHandleElId("profilerHead");
    
    //The Buttons in the head should not be valid drag handles; 
    //they are comprised of anchor elements, which DD allows us
    //to disclude as handles.
    DD.addInvalidHandleType("a");
    
    //Drag and Drop performs better when you use the dragOnly
    //setting for elements that can be moved but that don't
    //have any DD interactions with other page elements:
    DD.dragOnly = true;


    // Instantiate the menu and corresponding submenus. The first argument passed 
    // to the constructor is the id of the element in the DOM that represents 
    // the menu; the second is an object literal representing a set of 
    // configuration properties for the menu.
    var oMenu = new YAHOO.widget.Menu("productsandservices", { 
        context: ["menutoggle", "tl", "tr"]
     });

    // Call the "render" method with no arguments since the 
    // markup for this Menu instance is already exists in the page.
    oMenu.render();

    // Set focus to the Menu when it is made visible
    oMenu.subscribe("show", oMenu.focus);
    
    //Wire up the button to show the menu when clicked;
    YAHOO.util.Event.addListener("menutoggle", "click", oMenu.show, null, oMenu);

});</textarea>