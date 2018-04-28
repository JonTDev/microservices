// Constant for easy passing of variables.
const arg = { };

/**
 * Using the Mongoose-simpledb to search for a or many records
 * based on the arguments provided.
 *
 * @param {simpledb} db
 * @param {callback} cb
 */
function getMethod(db, cb) {
    // Used to hold the column string until the value comes into play
    // Then the key and value are placed together for the search query.
    let placeholder = '';

    // Ensuring that there are not any arguments.
    if(process.argv.length > 0) {

        // forEachAsync method to iterate through items asynchronously
        db.forEachAsync(process.argv, (k, v, done) => {

            // If the place holder has not been used then store
            // it for the next iterate.
            if(placeholder === '')

                // Storing it.
                placeholder = v;
            else if(placeholder === '_id') {

                // _id does not search well with ReGex

                // Placing the value
                arg[placeholder] = v;

                // Starting the placeholder over.
                placeholder = '';

            } else {

                // Setting the value and resetting the placeholder.
                arg[placeholder] = new RegExp(v, 'i');
                placeholder = '';
            }

            // If this is the last iterate
            if(done() === false) {

                // Performing the search.  Providing the arguments found.
                // Sending the results directly back to the driver.
                db.Items.find(arg, cb);
            }
        })
    } else {

        // If no arguments passed then just sends the entire
        // schema to the driver.
        db.Items.find({ }, cb);
    }
}

// Exporting this module.
module.exports = getMethod;