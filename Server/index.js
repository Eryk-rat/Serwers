const WebSocket = require('ws');

const connectedClients = {};

const server = new WebSocket.Server({ port: 1234 });

server.on('connection', (socket) => {
  console.log('Client connected');

  socket.on('message', (message) => {
    const messageData = JSON.parse(message);
    if (messageData.type === 'login') {
      $id = messageData.userId;
      connectedClients[messageData.userId] = socket;

      console.log('Client connected login ' + $id)
    } else if (messageData.type === 'localization') {
      const targetSocket = connectedClients[messageData.targetUserId];
      if (targetSocket) {
        targetSocket.send(`user: ${messageData.userId}, longitude: ${messageData.content.longitude}, latitude: ${messageData.content.latitude}, type: ${messageData.type}`);
      }
      $longitude = messageData.content.longitude;
      $latitude = messageData.content.latitude;
      console.log('Client lozalization send : ' + $longitude + ' : ' + $latitude);
    }else if (messageData.type === "message"){
      const targetSocket = connectedClients[messageData.targetUserId];
      if (targetSocket) {
        targetSocket.send(`user: ${messageData.userId}, tresc: ${messageData.content.tresc}, type: ${messageData.type}`);
      }
    }else if (messageData.type === 'warning') {
      const targetSocket = connectedClients[messageData.targetUserId];
      if (targetSocket) {
        targetSocket.send(`user: ${messageData.userId}, longitude: ${messageData.content.longitude}, latitude: ${messageData.content.latitude}, type: ${messageData.type}`);
      }
      console.log('Client warning send: ');
    }else{
      console.log('inny typ wiadomości');
    }
  });

  socket.on('close', () => {
    console.log('Client disconnected');
  });
});