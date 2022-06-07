const orders=document.querySelectorAll(".order-remove");
if(orders){
    orders.forEach(e =>{
        e.addEventListener('click',()=>{
            console.log(e.attributes.id.textContent);
            if(confirm('weet je het zeker dat je deze order wil verwijderen?')){
                fetch(`/admin/orders/delete/{$id}`,{
                    method: "DELETE"
                })
                .then(res => window.location.replace('/admin/orders'))
            }
            else{
                
            }
        })
    })
    
}