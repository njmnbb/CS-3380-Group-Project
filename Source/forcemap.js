/* This script is for the use of the d3 framework 
 * to make the force layout with nodes and links for every director
 * and their movies on the top 250
 */

var width;
var height,director;

//called by the php script and preforms the graphics in the svg
function forceMap(arr){
  
  //get dimensions of the container
  width = parseInt($("#tableContainer").css("width"));
  height = parseInt($("#tableContainer").css("height"));
   
   //create graph object to store the nodes (circles) and links (lines) that connect the circles)
   var graph= {
       "nodes" :[],
       "links" : [],

   }
     director = arr[0];
     //empty the container 
    $("#tableContainer").empty();

     //push the first node as it is the director and all other nodes will link to the director
     graph.nodes.push({"name": arr[0]});
   	 graph.links.push({"source": 0 ,"target": 0});
   
   //push every other node into the array with a target value pointing to the director node
   for (var i = 1; i < arr.length; i++) {
   	graph.nodes.push({"name": arr[i]});
   	graph.links.push({"source": i ,"target": 0});
   };
   
   //create the svg
   var svg = d3.select('#tableContainer').append('svg')
   			.attr("width", width)
   			.attr('height', height);
  
  //create the layout
    var force = d3.layout.force()
    		.size([width,height])
    		.gravity(.05)
    		.charge(-100)
    		.nodes(graph.nodes)
    		.links(graph.links)
            .start();

    force.linkDistance(width/4);

//center the director node
    force.on("tick", function() {
     graph.nodes[0].x = width / 2;
     graph.nodes[0].y = height / 2;
   });


//append all links to the svg
  var link = svg.selectAll('.link')
    .data(graph.links)
    .enter().append('line')
    .attr('class', 'link');

//create a g tag for all circles to sit in, this is also where the mouse action is checked
 var node = svg.selectAll('.node')
    .data(graph.nodes)
    .enter().append('g')
    .attr('class', 'node')
    .on('dblclick',clickAction)
    .on('mouseover', hover)
    .on('mouseout', function(){
    	            d3.select(this).select("circle").transition()
	                .duration(750)
	                .style("fill-opacity", .5)
	                .attr("r",width/25);})
    .call(force.drag);
 
 //append circles to the nodes
  node.append('circle')
      .attr('class','circle')
      .attr('fill-opacity',.5)
      .attr('r',width/25);
      //.call(force.drag);

//append the text of the name of the movie to the node as well
node.append("text")
      .attr("class",'text')
      .attr("stroke","black")
      .attr("dx", 12)
      .attr("dy", ".35em")
      .text(function(d) { return d.name });

var circles = d3.selectAll('.circle');

    //recalculate the source and position of the nodes and links on every movement (tick)
    force.on('tick', function(){
    	  
        
        link.attr('x1', function(d) { return d.source.x; })
        .attr('y1', function(d) { return d.source.y; })
        .attr('x2', function(d) { return d.target.x; })
        .attr('y2', function(d) { return d.target.y; });

node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"});

    });

    force.start();

   console.dir(graph);
   console.dir(force);
}

//make the circe grow and become opaque on hover
function hover(){
			d3.select(this).select("circle").transition()
	                .duration(750)
	                .style("fill-opacity", 1)
	                .attr("r",(width/25)*2);
}

//when double clicked create an info pane that contains a form to like, dislike, 
//and submit a review of the selected movie
function clickAction(){
   var d = document.createElement('div');
   
   if(document.getElementById('infopane') != null){
       var s = $(this).children(':last').text();
   $("#head").text(s);

   }else{

   $(d).appendTo('#content')
                .attr('id','infopane')
                .attr('class','faded')
                .css('height',300)
                .css('width',200);
                

   var s = $(this).children(':last').text();
   
   var like = "<input type =submit   class= panebutton  name = like value = Like></input>";
   var dlike = "<input type = submit class = panebutton name = dislike value = Dislike></input>";
   var review = "<textarea id = review name = review placeholder= \"Write a Review\"></textarea>";
   var title = "<input type= hidden name= movieTitle value = "+"\""+s+"\""+"></input>";
   console.log(s);
   var submit = "<input type= submit class= panebutton name= submitPane></input>";
     
   $(d).append('<h3 align = center id = head>'+s+'</h1>')
       .append("<h4 align = center>"+director+"</h4>")
       //.append(favorite)
       .append(like)
       .append(dlike)
       .append(review)
       .append(submit)
       .append(title);
       
}

}


