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
var contacts = [];
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

    client.query('SELECT count(c.id) as nb_contacts FROM contact c GROUP BY statut', function(err, results) {
        //if (err) throw err;
        resultats.nb_contacts_unread = results[0].nb_contacts;
        resultats.nb_contacts_read = results[1].nb_contacts;
        //resultats.nb_categories = results[0].nb_categories;
        
    });

    client.query('SELECT count(c.id) as nb_messages, MONTH(created) as month FROM contact c GROUP BY YEAR(c.created), MONTH(c.created)', function(err, results) {
        contacts = results;
         
    })

    socket.on('contact', function(message) {
        client.query('INSERT INTO contact (lastname, firstname, sender, subject, message, created) VALUES ("' + message.lastname + '", "' + message.firstname + '", "' + message.email + '", "' + message.subject + '","' + message.message + '", "' + message.created + '")', function(err, results) {
            if(err) throw err;
            resultats.nb_contacts++;
            io.sockets.emit('wdgt_content',resultats); 
        })
    })

    io.sockets.emit('wdgt_contact',contacts);
    io.sockets.emit('wdgt_content',resultats); 
});