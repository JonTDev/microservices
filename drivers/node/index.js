// Taking out the first two arguments.
// It is the root directory and file name.
process.argv.shift();
process.argv.shift();


const
    // Used for writing a small log because output is limited.
    fs = require('fs'),

    // used to easily connect and perform MongoDB queries.
    simpledb = require('mongoose-simpledb'),

    // The actual service.
    run = require(process.argv.shift()),

    // Logging Path.
    logErrorFile = `${__dirname}/log/error.log`,

    // Response for proper variable calling.
    response = {
        status : 200
    };

// Starting
start();


/**
 * Basically it is the start after the start.
 * We initialize MongoDB, gather our schemas,
 * and finish our response here.
 */
function start() {
    // MongoDB connection, settings and callback
    simpledb.init({
        // MongoDB connection String.
        connectionString: 'mongodb://localhost/company',

        // Location of the Schemas.
        modelsDir : `${__dirname}/schemas`

        // Callback (errorObject, Database Object "dbo")
    }, (err, db) => {

        // Checking for errors
        if(err)
            return writeErr(err);

        // Adding asynchronous forEach to the dbo.
        // This allows one initialization and easy access.
        db.forEachAsync = forEachAsync;

        // Ruuning Service and passing the dbo,
        // Error Object and Results
        run(db, (err, results) => {

            // Checking for errors.
            if(err)
                return writeErr(err);

            // Adding the results to the response.
            response.results = results;

            // Response is prepared, now I am seing it.
            // Callback (str that is ready to output.
            sendRes((str) => {

                // Output String
                console.log(str);

                // Stopping.
                process.exit();
            });
        });
        // TODO: Break the callbacks up for better and easier code.
    });
}


/**
 * This was used for fast error writing.
 * Normally we would use something like Winston for logging,
 * however this is justa  sample and does not include proper logging.
 * @param err {Error}
 */
function writeErr(err) {

    // Preparing message to log.
    let messageToWrite = {
        time : new Date().getTime(),
        message : err.message
    };

    // Accessing the current log file.
    // Callback (Error Object and File)
    fs.readFile(logErrorFile, (erro, file) => {

        // Checking if there were any errors accessing the file.
        if(erro) {

            // If there were errors stopping process.
            console.log(err);
            process.exit();
        }

        // Adding to the Log File with the current message.
        file += `\n${JSON.stringify(messageToWrite)}`;

        // Writing the new log message.
        fs.writeFile(logErrorFile, file, (err) => {

            // Ensuring there are not any errors logging the errors.
            if(err)
                // If so push to console.
                console.log(err);

            // Creating the response to the Access Point.
            response.message = 'System Failure';

            // Stating system failure.
            response.status = 500;

            // Sending the Failed Response.
            return sendRes((str) => {

                // String is prepared and output.
                console.log(str);

                // Stopping app.
                process.exit();
            });
        });
        // TODO:  Break callbacks up for better and easier code.
    })
}

/**
 * Prepares the response for any needed additions and makes it
 * all accessible from one spot.
 * @param cb Callback {Function}
 */
function sendRes(cb) {
    cb(JSON.stringify(response));
}

/**
 * A forEach loop that is asynchronous.
 * Takes the object that is passed and splits it into keys and values.
 * Passes in this order (key, value, done)
 *
 * Done is a function that does just that.  Says you are done with
 * this iteration.  It will return false when the iteration is over.
 *
 * @example
 * let arr = [1, 2, 3, 4, 5];
 * forEachAsync(arr, (k, v, done) {
 *      console.log(k); // returns 0-4
 *      console.log(v); // returns 1-5
 *      if(done() === false) {
 *          // TO DO:  Perform ending task here.
 *      }
 * }
 * @param obj {Object}|{Array}
 * @param cb Callback {Function}
 */
function forEachAsync(obj, cb) {

    // If the object is still in original form re-creating it.
    if(!obj.k) {
        obj = {
            // Getting Keys
            k : Object.keys(obj),

            // Getting Values.
            v : Object.values(obj)
        }
    }

    /**
     * Checks if the obj.k is at 0.
     * If not then recursively runs the method using the new object.
     * If it is then it returns false to signal iterations are over.
     * @return {boolean}
     */
    let done = function() {

        // Checking if obj.k is empty.
        if(obj.k.length === 0)

            // Returning false if obj.k is empty.
            return false;

        // Recursively running this function.
        forEachAsync(obj, cb);
    };

    // Setting a timeout to prevent stack overflow.
    // really could probably do 1 second and that would be enough.
    // However I used five.
    setTimeout(() => {

        // Callback using the shift function to empty the keys and values.
        cb(obj.k.shift(), obj.v.shift(), done);
    }, 5);
}