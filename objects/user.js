User = {
	app : null,
	init: function(){
		User.app = angular.module('user', []);
		User.bindControllers();
	},
	bindControllers : function(){
		User.app.controller('userCtrl', function($scope, $http) {
			User.userCtrl.init($scope, $http);
		});
	},
	userCtrl: {
		init: function($scope, $http){
			$scope.user         = { Id : "", Name : "", Email : "", Pwd1 : "", Pwd2 : "" };
			$scope.editing      = false;
			$scope.have_error   = true;
			$scope.confirmation = true;
			$scope.predicate    = 'Id';
			$scope.reverse      = false;

			$scope.edit     = function(id)       { User.userCtrl.edit($scope, id); }
			$scope.new      = function()         { User.userCtrl.new($scope); }
			$scope.test     = function()         { User.userCtrl.test($scope); }
			$scope.save     = function()         { User.userCtrl.save($scope, $http); }
			$scope.remove   = function(id)       { User.userCtrl.remove($scope, $http, id); }
			$scope.order    = function(predicate){ User.userCtrl.order($scope, predicate); }
			$scope.order_by = function(user)     { return User.userCtrl.order_by($scope, user); }

			User.userCtrl.get_user_list($scope, $http);
			User.userCtrl.bind_watchs($scope);
		},
		order_by: function($scope, user){
			return ($scope.predicate == "Id") ? parseInt(user[$scope.predicate]) : user[$scope.predicate];
		},
		order: function($scope, predicate){
			$scope.reverse   = ($scope.predicate === predicate) ? !$scope.reverse : false;
			$scope.predicate = predicate;
		},
		bind_watchs: function($scope){
			$scope.$watch('user.Pwd1',   function() {$scope.test();});
			$scope.$watch('user.Pwd2',   function() {$scope.test();});
			$scope.$watch('user.Name',   function() {$scope.test();});
			$scope.$watch('user.Email',  function() {$scope.test();});
		},
		reset: function($scope){
			$scope.editing    = false;
			$scope.user.Id    = 0;
			$scope.user.Name  = '';
			$scope.user.Email = '';
			$scope.user.Pwd1  = '';
			$scope.user.Pwd2  = '';
			$scope.userForm.$setPristine();
			User.userCtrl.test($scope);
			$("[name=name]").focus();
		},
		edit : function($scope, id){
			$scope.editing    = true;
			var register = $.grep($scope.users, function(e){ return e.Id == id; });
			$scope.user.Id    = register[0].Id;
			$scope.user.Name  = register[0].Name;
			$scope.user.Email = register[0].Email;
			$scope.user.Pwd1  = '';
			$scope.user.Pwd2  = '';
			User.userCtrl.test($scope);
		},
		new: function($scope){
			User.userCtrl.reset($scope);
		},
		get_user_list: function($scope, $http){
			$http.get("data.php?method=get_user_list").success(function(response){
				$scope.users = response.record;
			});
		},
		test: function($scope){
			$scope.have_error = ($scope.userForm.$pristine) || (
								($scope.userForm.name.$invalid) ||
								($scope.userForm.email.$invalid) || 
								($scope.userForm.pwd1.$invalid) ||
								($scope.userForm.pwd2.$invalid) ||
								($scope.userForm.pwd1.$modelValue != $scope.userForm.pwd2.$modelValue)
								);
			$scope.confirmation = ($scope.userForm.pwd1.$modelValue != $scope.userForm.pwd2.$modelValue);
		},
		save : function($scope, $http){
			$http({
				url     : 'data.php?method=save',
				method  : "POST",
				headers : {'Content-Type': 'application/x-www-form-urlencoded'},
				data    : $.param($scope.user)
				}).success(function(data, status, headers, config) {
					User.userCtrl.get_user_list($scope, $http);
					User.userCtrl.reset($scope);
					$scope.data = data;
				}).error(function(data, status, headers, config) {
					alert('Houve um erro ao salvar o registro.');
			});
		},
		remove : function($scope, $http, id){
			$http({
				url     : 'data.php?method=remove',
				method  : "POST",
				headers : {'Content-Type': 'application/x-www-form-urlencoded'},
				data    : $.param({Id: id})
				}).success(function(data, status, headers, config) {
					User.userCtrl.get_user_list($scope, $http);
					$scope.data = data;
				}).error(function(data, status, headers, config) {
					alert('Houve um erro ao remover o registro.');
			});
		},
	},
}

User.init();