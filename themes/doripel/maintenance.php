<style>
.workcontrol_maintenance_content{
    display: block;
    background: red;
    position: fixed;
    width: 100%;
    height: 100%;
	background: #fff url(admin/_img/website-maintenance.png) no-repeat center center;
}

.workcontrol_maintenance_content .maintenance_box{
    display: block;
    width: 100%;
	height: 345px;
    margin: 10% auto;
    max-width: 90%;
		text-align: center;

}

.workcontrol_maintenance_content .maintenance_box h1{
    font-size: 1.6em;
    font-weight: 800;
    color: red;
    text-shadow: 1px 1px 0 #eee;
}

.workcontrol_maintenance_content .maintenance_box p{
    margin: 15px 0;
}
</style>
<article class="workcontrol_maintenance_content">
    <div class="maintenance_box">
        <h1>Desculpe, estamos em manutenção!</h1>
				
				<div style="height: 350px;" ></div>			
        <p>Neste momento estamos trabalhando para melhorar ainda mais sua experiência em nosso site.</p>
        <p><b>Por favor, volte em algumas horas para conferir as novidades!</b></p>
        <em>Atenciosamente <?= SITE_NAME; ?></em>
    </div>
</article>
