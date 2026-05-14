
var MikroNode = require('./node_modules/mikronode/dist/mikronode.js');

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
   res.sendFile(path.join(__dirname, 'mikrotikgraph.html'));
})

// Create A new Connection
io.on('connection', socket => {
   console.log('User Connected to Server');

   socket.on("connectdevice", deviceIP => {

      var device = new MikroNode(deviceIP, "9728");
      device.setDebug(MikroNode.DEBUG);

         device.connect().then(([login])=>login('userapi','Api@@@0007')).then(conn=>{
            // When all channels are marked done, close the connection.
            console.log('Server Conneted to Mikrotik API');
            //conn.closeOnDone(false);

         socket.on('disconnect', ()=>{
            console.log("A user disconnected..");
         })

         socket.on("username", username => {
            console.log("Username is : " + username);

               // Connect to Mikrotik 
                  //************************************//
                     
                     var channel1=conn.openChannel();
                     channel1.closeOnDone(true);

                        channel1.write('/interface/monitor-traffic',{
                           'interface': '<pppoe-'+username+'>',
                           'once': true
                        }).then(p =>{
                           console.log("ALERT.... Done Interface Changed");
                           //let val = MikroNode.resultsToObj(p.data);
                           var parsed = MikroNode.resultsToObj(p.data);
                           console.log(parsed);
         
                           socket.emit("traffic", parsed);  // Send Traffic to Client
                           socket.emit("download", Math.round((((parseInt(parsed[0]["tx-bits-per-second"] / 1000) / 1000)) * 1.15), 0)); 
                           socket.emit("upload", Math.round((((parseInt(parsed[0]["rx-bits-per-second"] / 1000) / 1000))), 0)); 
                     
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

})
// Initialize Server
http.listen(port,()=>{
   console.log('App Listening on port');
})


