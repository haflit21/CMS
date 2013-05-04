var app = require('http').createServer(function (req, res) { 
    console.log('Un utilisater a visité la page');
});
var io = require('socket.io').listen(app)
var mysql = require('mysql')
var client = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'root'
});

client.connect();

app.listen(3232);

io.sockets.on('connection', function (socket) {
    var TEST_DATABASE = 'chrisSymfony';
    var TEST_TABLE = 'cm_contents';
    var resultats = {};

    client.query('USE '+TEST_DATABASE);

    client.query('SELECT count(c.id) as nb_contents FROM cm_contents c', function(err, results) {
        //if (err) throw err;
        resultats.nb_contents = results[0].nb_contents;
        //resultats.nb_categories = results[0].nb_categories;
        console.log('Nombre de publications : ' + results[0].nb_contents); 
        
    });
    
    io.sockets.emit('wdgt_content',results); 
});