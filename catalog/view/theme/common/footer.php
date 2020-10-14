		<!-- jQuery  -->

		<!-- <script src="assets/plugins/apex-charts/apexcharts.min.js"></script>
		<script src="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
		<script src="assets/plugins/jvectormap/jquery-jvectormap-us-aea-en.js"></script>
		<script src="assets/pages/jquery.analytics_dashboard.init.js"></script>
		
		 -->
		 <script src="assets/js/app.js"></script>
		 <script>
			$(function(e){
				$('.datepicker').daterangepicker({
		            singleDatePicker: !0,
		            showDropdowns: !0,
		            minYear: 2020,
		            maxYear: parseInt(moment().format("YYYY"), 10),
		            locale: {
				      format: 'YYYY-MM-DD'
				    }
		        });
			});
		</script>
	</body>
</html>