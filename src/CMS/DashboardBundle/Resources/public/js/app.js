var app = require('http').createServer(function (req, res) { 
    console.log('Un utilisater a visité la page');
});
var io = require('socket.io').listen(app);
var mysql = require('mysql')
var client = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: 'root'
});

client.connect();

app.listen(3232);
var resultats = {};
io.sockets.on('connection', function (socket) {
    var TEST_DATABASE = 'chrisSymfony';
    var TEST_TABLE = 'cm_contents';
    

    client.query('USE '+TEST_DATABASE);

    client.query('SELECT count(c.id) as nb_contents FROM cm_contents c', function(err, results) {
        //if (err) throw err;
        resultats.nb_contents = results[0].nb_contents;
        //resultats.nb_categories = results[0].nb_categories;
        
    });

    client.query('SELECT count(c.id) as nb_categories FROM cm_categories c', function(err, results) {
        //if (err) throw err;
        resultats.nb_categories = results[0].nb_categories;
        //resultats.nb_categories = results[0].nb_categories;
        
    });

    client.query('SELECT count(c.id) as nb_contacts FROM contact c WHERE statut=0', function(err, results) {
        //if (err) throw err;
        console.log(results[0].nb_contacts);
        resultats.nb_contacts = results[0].nb_contacts;
        //resultats.nb_categories = results[0].nb_categories;
        
    });

    socket.on('contact', function(message) {
        console.log(message);
        client.query('INSERT INTO contact (lastname, firstname, sender, subject, message, created) VALUES ("' + message.lastname + '", "' + message.firstname + '", "' + message.email + '", "' + message.subject + '","' + message.message + '", "' + message.created + '")', function(err, results) {
            if(err) throw err;
            resultats.nb_contacts++;
            io.sockets.emit('wdgt_content',resultats); 
        })
    })

    io.sockets.emit('wdgt_content',resultats); 
});