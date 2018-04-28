/**
 * The provided parameter only accepts the _id "ObjectId"
 *
 * Using Mongoose-simpledb to pass schema and perform
 * the lookup then delete.
 *
 * @param {simpledb} db
 * @param {callback} cb
 */
function deleteMethod(db, cb) {

    // Getting the ID from the passed argument.
    let id = process.argv.shift();

    // Searching for the record.
    db.Items.find({ _id : id }).remove( returnMe );

    /**
     * This method literally just makes it easier to read the code.
     *
     * Passes whether or not the request was successful back.
     *
     * @param {Error} err
     * @param {Object} result
     * @return {*}
     */
    function returnMe(err, result) {
        if(result.ok !== 1)
            return cb(err, `Unsuccessful at deleting ${id}`);
        else
            return cb(err, `Successful at deleting ${id}`);
    }

}

// Exporting the module.
module.exports = deleteMethod;