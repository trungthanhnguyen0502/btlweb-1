function Ticket(obj){
    this.id           = -1;
    this.subject      = "";
    this.content      = "";
    this.create_by    = -1;
    this.status       = 1;
    this.priority     = 1;
    this.deadline     = new Date();
    this.assigned_to  = -1;
    this.rating       = -1;
    this.team_id      = -1;
    this.resolved_at  = new Date();
    this.closed_at    = new Date();
    this.created_at   = new Date();
    this.updated_at   = null;
    this.deleted_at   = null;
    for (var pro in obj)  this[pro] = obj[pro];
};

function TicketThread(obj){
    this.id          = -1;
    this.ticket_id   = -1;
    this.employee_id = -1;
    this.content     = "";
    this.type        = -1;
    this.note        = "";
    this.created_at  = null;
    this.updated_at  = null;
    for (var pro in obj)
      this[pro] = obj[pro];
};

function TicketImage(obj){
    this.id_ticket   = -1;
    this.url_image   = "";
    for (var pro in obj) 
         this[pro] = obj[pro];
};

function TicketRead(obj){
    this.ticket_id = -1;
    this.status = -1;
    for (var pro in obj)  
        this[pro] = obj[pro];
}

function TicketRelated(obj){
    this.ticket_id       = -1;
    this.employee_id     = -1;
    for (var pro in obj) 
      this[pro] = obj[pro];
}

function TicketAttribute(obj){
    this.id = -1;
    this.status = "";
    this.priority = "";
    this.rating = "";
    this.reopened = "";
    for (var pro in obj) 
       this[pro] = obj[pro];
}
