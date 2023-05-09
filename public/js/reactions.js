function sendLike(event){
    console.log('da');
    console.log(event.getAttribute('reaction-target'));
    let data_body = "postId=" + event.getAttribute('reaction-target');
    fetch("/app/like", { 
        method: "POST",
        body: data_body,
        headers:{
            'Content-Type': 'application/x-www-form-urlencoded'
        }
        })
    .then((response) => {
        if (response.status !== 200) {           
            return Promise.reject();
        }
        return response.text()
    })
    .catch();
    window.location.reload();
}

const toSqlDatetime = (inputDate) => {
    const date = new Date(inputDate)
    const dateWithOffest = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
    return dateWithOffest
        .toISOString()
        .slice(0, 19)
        .replace('T', ' ')
}

function sendComment(event){
    let textArea = event.previousSibling.previousSibling;
    let data_body = "postId=" + textArea.getAttribute('reaction-target') + "&comment=" + textArea.value + "&date=" + toSqlDatetime(new Date());
    console.log(data_body);
    fetch("/app/comment", {
        method: "POST",
        body: data_body,
        headers:{
            'Content-Type': 'application/x-www-form-urlencoded'
        }
        })
    .then((response) => {
        if (response.status !== 200) {       
            return Promise.reject();
        }
        return response.text()
    })
    .catch();

    textArea.innerHTML = '';

    window.location.reload();
}