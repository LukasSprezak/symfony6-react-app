import React, { useEffect, useState } from 'react'
import { Link, useNavigate } from "react-router-dom"
import authFetch from "../../infrastructure/axios/axios";
import store from "../../store";
import {auth} from "../../store/auth/AuthAction";
import {Button, Container, Nav, Navbar, NavLink} from 'react-bootstrap';

const Dashboard: React.FC = () => {
    const navigate = useNavigate();
    const [user, setUser] = useState<object|any>({})

    useEffect((): void => {
        if ("" === localStorage.getItem('token') || null === localStorage.getItem('token')) {
            navigate("/");
        } else {
            getUser()
        }
        console.log(user);
    },[])

    const getUser = (): void => {
        authFetch.get('/api/users')
            .then((response) => {
                setUser(response.data['hydra:member'][0])
            })
            .catch((event): void => {
                console.log(event)
            });
    }

    const logoutAction = (): void => {
        authFetch.get(`/api/logout`)
            .then((response): void => {
                store.dispatch(auth({
                    token: '',
                    refresh_token: ''
                }))
                localStorage.setItem('token', "")
                navigate("/");
            })
            .catch((event): void => {
                console.log(event)
            });
    }
    // const logoutAction = (): void => {
    //     authFetch.post('/api/logout',{}, {
    //         headers: {
    //             Authorization: 'Bearer ' + localStorage.getItem('token')
    //         }})
    //         .then((response): void => {
    //             localStorage.setItem('token', "")
    //
    //             navigate("/");
    //         })
    //         .catch((event): void => {
    //             console.log(event)
    //         });
    // }

    return (
        <><Navbar bg="light" expand="lg" className="navbar-light">
            <Container>
                <Navbar.Brand>Dashboard</Navbar.Brand>
                <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                <Navbar.Collapse id="basic-navbar-nav">
                    <Nav className="ms-auto">
                        <Nav.Link>
                            <Button className="btn-secondary btn-lg">
                                <Link to="/dashboard" className="nav-link" style={{color: "white"}}>Dashboard</Link>
                            </Button>
                        </Nav.Link>
                        <Nav.Link>
                            <Button className="btn-secondary btn-lg">
                                <Link to="/product-list" className="nav-link" style={{color: "white"}}>Products</Link>
                            </Button>
                        </Nav.Link>
                        <Nav.Link>
                            <Button className="btn-success btn-lg" onClick={() => logoutAction()}>Logout</Button>
                        </Nav.Link>
                    </Nav>
                </Navbar.Collapse>
            </Container>
        </Navbar><Container>
            <h2 className="text-center mt-5">Welcome, {user.username}!</h2>
        </Container></>
    );
}

export default Dashboard;
