
var MikroNode = require('./node_modules/mikronode/dist/mikronode.js');
var device = new MikroNode('115.186.148.250');
device.setDebug(MikroNode.DEBUG);
// Connect to MikroTik device


// Constants
const express = require('express');
const app = express();
const path = require('path');

const http = require('http').Server(app);
const port = process.env.PORT || 8111;

const io = require('socket.io')(http);



// Handling data
// ROUTE
app.get('/',(req,res) => {
   //res.json("get request");
   res.sendFile(path.join(__dirname, 'mikrotikgraph.html'))
})

// Create A new Connection
io.on('connection', socket => {
   console.log('User Connected to Server');

   // Connect to Mikrotik 
   //************************************//

   device.connect().then(([login])=>login('admin','Khyber@007')).then(conn=>{
   // When all channels are marked done, close the connection.
   console.log('connected');

   conn.closeOnDone(true);

   var channel1=conn.openChannel();
   channel1.closeOnDone(true);

   console.log('Connected Now...');

   socket.on('disconnect', ()=>{
      console.log("A user disconnected..");
   })

   socket.on("username", username => {
      console.log("Username is : " + username);

            // get only a count of the addresses.
            channel1.write('/interface/monitor-traffic',{
               'interface': '<pppoe-'+username+'>',
               'once': true
            }).then(p =>{
               console.log("ALERT.... Done Interface Changed");
               //let val = MikroNode.resultsToObj(p.data);
               var parsed = MikroNode.resultsToObj(p.data);
               console.log(parsed);

               socket.emit("traffic", parsed);  // Send Traffic to Client
         
            }).catch(error=>{
               console.log("Error result ",error);
            });
            console.log('Wrote');
         }
         ).catch(error=>{
            console.log("Error logging in ",error);
         });

   })

})

// Initialize Server
http.listen(port,()=>{
   console.log('App Listening on port');
})


