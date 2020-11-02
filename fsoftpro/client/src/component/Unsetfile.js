import React, { useState } from "react";
import axios from 'axios';

export const Unsetfile = () => {
  const [status, setStatus] = useState();
  const [message, setMessage] = useState();

  const handleClick = () => {
    console.log('click');
    let url = "http://localhost:5000/removefile";
    axios
      .post(url)
      .then((res) => {
        setStatus("success");
        setMessage("Delete file success");
        console.log(res.data);
      })
      .catch((error) => {
        setStatus("error");
        setMessage("Fail Delete file");
      });
  };

  return (
    <div>
      {status == "success" ? (
        <div className="alert alert-success" role="alert">
          {message}
        </div>
      ) : (
        ""
      )}
      {status == "error" ? (
        <div className="alert alert-danger" role="alert">
          {message}
        </div>
      ) : (
        ""
      )}
      <button
        type="button"
        className="btn btn-outline-primary"
        onClick={handleClick}
      >
        Delete File
      </button>
    </div>
  );
};

export default Unsetfile;