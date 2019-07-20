USE QL_SACH
GO



CREATE FUNCTION fc_hadExist_Book(@name nvarchar(255)) RETURNS INT
AS
BEGIN
	Declare @selected_name nvarchar(255) = Null;
	Select @selected_name = bk.[name] From BOOK as bk
		Where bk.[name] = @name
	If (@selected_name Is null) Return 0;
	Return 1;
END
GO

CREATE FUNCTION fc_hadExist_Author(@name nvarchar(255)) RETURNS INT
AS
BEGIN
	Declare @selected_name nvarchar(255) = Null;
	Select @selected_name = au.[name] From AUTHOR as au
		Where au.[name] = @name
	If (@selected_name Is null) Return 0;
	Return 1;
END
GO

CREATE FUNCTION fc_hadExist_Category(@name nvarchar(255)) RETURNS INT
AS
BEGIN
	Declare @selected_name nvarchar(255) = Null;
	Select @selected_name = ca.[name] From CATEGORY as ca
		Where ca.[name] = @name
	If (@selected_name Is null) Return 0;
	Return 1;
END
GO

CREATE FUNCTION fc_hadExist_NXB(@name nvarchar(255)) RETURNS INT
AS
BEGIN
	Declare @selected_name nvarchar(255) = Null;
	Select @selected_name = nxb.[name] From NXB as nxb
		Where nxb.[name] = @name
	If (@selected_name Is null) Return 0;
	Return 1;
END
GO



CREATE FUNCTION fc_findByName_Book(@name nvarchar(255)) RETURNS TABLE
AS
RETURN
(
	Select * From BOOK as bk Where bk.[name] = @name
)
GO

CREATE FUNCTION fc_findByName_Author(@name nvarchar(255)) RETURNS TABLE
AS
RETURN
(
	Select * From AUTHOR as au Where au.[name] = @name
)
GO

CREATE FUNCTION fc_findByName_Category(@name nvarchar(255)) RETURNS TABLE
AS
RETURN
(
	Select * From CATEGORY as ca Where ca.[name] = @name
)
GO

CREATE FUNCTION fc_findByName_NXB(@name nvarchar(255)) RETURNS TABLE
AS
RETURN
(
	Select * From NXB as nxb Where nxb.[name] = @name
)
GO



CREATE FUNCTION fc_findParentNode_Category(@node varchar(50)) RETURNS nvarchar(MAX)
AS
BEGIN
	Declare @parent varchar(50);
	Select @parent = id_parent From CATEGORY Where @node = id
	
END
GO

CREATE FUNCTION fc_createTree_Category(@categories nvarchar(MAX)) RETURNS @Result TABLE
(
	id int IDENTITY(1,1),
	[node] nvarchar(MAX)
)
AS
BEGIN
	Insert Into @Result([node])
		Select value as node From string_split(@categories, '>')
	Return;
END
GO

CREATE FUNCTION fc_isLeafNode_Category(@node nvarchar(MAX)) RETURNS INT
AS
BEGIN
	If (@node IN(Select par.[name] From CATEGORY as par, CATEGORY as child Where child.id_parent = par.id))
		Return 0;
	Return 1;
END
GO



CREATE FUNCTION fc_createID_Book()RETURNS VARCHAR(50)
AS
BEGIN
	Declare @count_number int;
	Select @count_number = COUNT(bk.id) From BOOK as bk
	Declare @id_result varchar(50) = '000000' + Convert(nvarchar(10), @count_number);
	Return 'book' + Right(@id_result, 6);
END
GO

CREATE FUNCTION fc_createID_Author()RETURNS VARCHAR(50)
AS
BEGIN
	Declare @count_number int;
	Select @count_number = COUNT(au.id) From AUTHOR as au
	Declare @id_result varchar(50) = '000000' + Convert(nvarchar(10), @count_number);
	Return 'author' + Right(@id_result, 6);
END
GO

CREATE FUNCTION fc_createID_Category()RETURNS VARCHAR(50)
AS
BEGIN
	Declare @count_number int;
	Select @count_number = COUNT(ca.id) From CATEGORY as ca
	Declare @id_result varchar(50) = '000000' + Convert(nvarchar(10), @count_number);
	Return 'category' + Right(@id_result, 6);
END
GO

CREATE FUNCTION fc_createID_NXB()RETURNS VARCHAR(50)
AS
BEGIN
	Declare @count_number int;
	Select @count_number = COUNT(nxb.id) From NXB as nxb
	Declare @id_result varchar(50) = '000000' + Convert(nvarchar(10), @count_number);
	Return 'nxb' + Right(@id_result, 6);
END
GO

CREATE FUNCTION fc_createID_Image()RETURNS VARCHAR(50)
AS
BEGIN
	Declare @count_number int;
	Select @count_number = COUNT(img.id) From [IMAGE] as img
	Declare @id_result varchar(50) = '000000' + Convert(nvarchar(10), @count_number);
	Return 'image' + Right(@id_result, 6);
END
GO



CREATE PROCEDURE sp_saveNew_Author(@id varchar(50), @name nvarchar(255))
AS
BEGIN
	If ((@id Is null) Or (LEN(@id) = 0))
		Set @id = dbo.fc_createID_Author();
	Insert Into AUTHOR Values(@id, @name);
END
GO

CREATE PROCEDURE sp_saveNew_Category(@categories nvarchar(MAX))
AS
BEGIN
	Declare @tree table(node nvarchar(MAX));
	Insert Into @tree 
		Select [node] From dbo.fc_createTree_Category(@categories);

	--> Kiểm tra từng node đã tồn tại chưa, nếu chưa thì thêm vào, rồi thì bỏ qua
	Declare @size int, @index int = 0, @id_root_nodes nvarchar(255) = Null;
	Select @size = COUNT(node) From @tree
	While (@index < @size)
	Begin
		-->Lấy phần tử thứ i ra
		Declare @node nvarchar(MAX);
		Select TOP(1) @node = [node] From @tree

		-->Kiểm tra node lấy ra đã tồn tại hay chưa, nếu rồi thì bỏ qua, chưa thì tạo mới
		Declare @is_exist int = dbo.fc_hadExist_Category(@node);
		If (@is_exist = 1) 
		Begin
			-->Cập nhật lại id_root_nodes
			Select @id_root_nodes = id From dbo.fc_findByName_Category(@node);
		End
		Else
		Begin
			Declare @new_id varchar(50) = dbo.fc_createID_Category();
			Insert Into CATEGORY Values(@new_id, @node, @id_root_nodes);
			-->Cập nhật lại id_root_nodes
			Set @id_root_nodes = @new_id;
		End

		-->Xóa phần tử vừa đọc trong @tree
		Delete TOP(1) From @tree

		-->Tăng biến đếm
		Set @index = @index + 1;
	End

END
GO

CREATE PROCEDURE sp_saveNew_NXB(@id varchar(50), @name nvarchar(255))
AS
BEGIN
	If ((@id Is null) Or (LEN(@id) = 0))
		Set @id = dbo.fc_createID_NXB();
	Insert Into NXB Values(@id, @name);
END
GO

CREATE PROCEDURE sp_saveNew_Book(@name nvarchar(255), @price int, @rate float, @author nvarchar(255), @categories nvarchar(MAX), @nxb nvarchar(255), @descript ntext, @url_image nvarchar(MAX))
AS
BEGIN
	-->Kiểm tra sách đã tồn tại hay chưa, nếu chưa có thì tạo mới, ngược lại thì bỏ qua
	Declare @is_ExistBook int = dbo.fc_hadExist_Book(@name);
	If (@is_ExistBook = 0)
	Begin
		-->Kiểm tra trường hợp thể loại = null
		If (@categories Is Null) Set @categories = 'Other';
		-->Thêm thể loại của sách
		Exec dbo.sp_saveNew_Category @categories;
		-->Kiểm tra lại thể loại chính của cuốn sách (node con cuối cùng)
		Declare @name_category nvarchar(MAX);
		Select TOP(1) @name_category = [node] From fc_createTree_Category(@categories) Order By [id] DESC;
		-->Kiểm tra thể loại có hợp lệ để lưu thông tin sách (là node con, không là cha của bất kỳ node nào khác), nếu là node cha thì tạo thêm một node con Other thuộc node cha đó để lưu thông tin
		Declare @is_LeafNodes int = dbo.fc_isLeafNode_Category(@name_category);
		If (@is_LeafNodes = 0)
		Begin
			Exec dbo.sp_saveNew_Category @categories;
		End
		-->Thiết lập giá trị cho id của thể loại chính cuốn sách
		Declare @id_category varchar(50) = null;
		Select @id_category = id From dbo.fc_findByName_Category(@name_category);
	
		-->Kiểm tra trường hợp tác giả và nxb = null
		If (@author Is Null) Set @author = 'Other';
		If (@nxb Is Null) Set @nxb = 'Other';
		-->Kiểm tra tồn tại và gán giá trị cho author và nxb
		Declare @is_exist_author int = dbo.fc_hadExist_Author(@author);
		Declare @is_exist_nxb int = dbo.fc_hadExist_NXB(@nxb);
		Declare @id_author varchar(50);
		Declare @id_nxb varchar(50);
		-->Thiết lập giá trị id cho author
		If (@is_exist_author = 1)
			Select @id_author = au.id From dbo.fc_findByName_Author(@author) as au
		Else
		Begin
			Set @id_author = dbo.fc_createID_Author();
			Exec dbo.sp_saveNew_Author @id_author, @author;
		End
		-->Thiết lập giá trị id cho nxb
		If (@is_exist_nxb = 1)
			Select @id_nxb = nxb.id From dbo.fc_findByName_NXB(@nxb) as nxb
		Else
		Begin
			Set @id_nxb = dbo.fc_createID_NXB();
			Exec dbo.sp_saveNew_NXB @id_nxb, @nxb;
		End
	
		-->Tạo mới id cuốn sách
		Declare @id_book varchar(50) = dbo.fc_createID_Book()
		-->Lưu thông tin sách mới vào table
		Insert Into BOOK Values(@id_book, @name, @price, @rate, @id_author, @id_category, @id_nxb, @descript);

		-->Kiểm tra url_image hợp lệ. Tạo id ảnh mới
		If (@url_image Is Not Null)
		Begin
			Declare @id_image varchar(50) = dbo.fc_createID_Image();
			Insert Into [IMAGE] Values(@id_image, @id_book, @url_image); 
		End

	End
END
