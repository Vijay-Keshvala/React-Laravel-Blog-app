import React, { useState } from "react";
import Editor from "react-simple-wysiwyg";
import { useForm } from "react-hook-form";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";

const CreateBlogs = () => {
    const [html, setHtml] = useState("");
    const [imageId, setImageId] = useState("");
    const navigate = useNavigate();

    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const onChange = (e) => {
        setHtml(e.target.value);
    };

    const formSubmit = async (data) => {
        const setData = { ...data, description: html, image_id: imageId };
    
        try {
            const res = await fetch("http://127.0.0.1:8000/api/blogs", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(setData),
            });
    
            const result = await res.json();
            console.log("API Response:", result);
    
            if (res.ok) {
                toast("Blog added successfully");
                navigate("/");
            } else {
                console.error("Failed to add blog:", result);
                toast.error("Failed to add blog: " + (result.message || "Unknown error"));
            }
        } catch (error) {
            console.error("Error adding blog:", error);
            toast.error("Network or server error. Check console for details.");
        }
    };
    
    const handleFileChange = async(e) =>{
         const file = e.target.files[0]
         const formData = new FormData()
         formData.append("image",file)

         const res = await fetch("http://127.0.0.1:8000/api/save-temp-img/",{
            method:"POST",
            body:formData
         });
         const result = await res.json()

        //  console.log(result);

        if (!file.type.startsWith("image/")) {
            alert("Only image files are allowed!");
            e.target.value = null; // Clear the file input
            return;
        }

        if(result.status == false){
            alert(result.errors.image)
            e.target.value = null
        }

        setImageId(result.image.id)
    }

    return (
        <div className="container mb-5">
            <div className="d-flex justify-content-between pt-5 mb-4">
                <h4>Create Blogs</h4>
                <a href="/" className="btn btn-dark">Back</a>
            </div>
            <div className="card border-0 shadow-lg">
                <form onSubmit={handleSubmit(formSubmit)}>
                    <div className="card-body">
                        <div className="mb-3">
                            <label className="form-label">Title</label>
                            <input
                                {...register("title", { required: true })}
                                type="text"
                                placeholder="Title"
                                className={`form-control ${errors.title ? "is-invalid" : ""}`}
                            />
                            {errors.title && <p className="invalid-feedback">Title field is required</p>}
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Short Description</label>
                            <textarea
                                {...register("shortDesc")}
                                className="form-control"
                                cols={30}
                                rows={3}
                            ></textarea>
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Description</label>
                            <Editor value={html}
                                containerProps={{ style: { height: '400px' } }}
                                onChange={onChange} />
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Image</label> <br />
                            <input onChange={handleFileChange} type="file" />
                        </div>

                        <div className="mb-3">
                            <label className="form-label">Author</label>
                            <input
                                {...register("author", { required: true })}
                                type="text"
                                placeholder="Author"
                                className={`form-control ${errors.author ? "is-invalid" : ""}`}
                            />
                            {errors.author && <p className="invalid-feedback">Author field is required</p>}
                        </div>

                        <button className="btn btn-dark">Create</button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default CreateBlogs;
