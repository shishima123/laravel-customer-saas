"use strict"

$(function () {
    let table = new UserTable();
    table.load('#user-table','#add-user');
    initMarkRemove('#user-table')
    initCancelSubscription()
});
